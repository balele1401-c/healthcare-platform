import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';
import '../../../../core/routing/app_routes.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_typography.dart';
import '../../../../shared/widgets/app_avatar.dart';
import '../../../../shared/widgets/app_badge.dart';
import '../../../../shared/widgets/app_button.dart';
import '../../../../shared/widgets/app_card.dart';
import '../../domain/models/appointment_model.dart';

class AppointmentSuccessScreen extends StatelessWidget {
  final AppointmentModel appointment;

  const AppointmentSuccessScreen({
    super.key,
    required this.appointment,
  });

  @override
  Widget build(BuildContext context) {
    final dateFormatted = DateFormat('EEEE, MMMM d, yyyy').format(appointment.dateTime);

    return Scaffold(
      backgroundColor: AppColors.background,
      body: SafeArea(
        child: LayoutBuilder(
          builder: (context, constraints) {
            final isDesktop = constraints.maxWidth >= 900;

            return Center(
              child: SingleChildScrollView(
                padding: EdgeInsets.symmetric(
                  horizontal: isDesktop ? AppSpacing.desktopMargin : AppSpacing.marginMobile,
                  vertical: AppSpacing.lg,
                ),
                child: ConstrainedBox(
                  constraints: const BoxConstraints(maxWidth: 580),
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      // 1. Success Animated Icon
                      Container(
                        width: 80,
                        height: 80,
                        decoration: BoxDecoration(
                          color: AppColors.successContainer,
                          shape: BoxShape.circle,
                          border: Border.all(color: const Color(0xFFA7F3D0), width: 1.5),
                        ),
                        child: const Center(
                          child: Icon(
                            Icons.check_circle_rounded,
                            color: AppColors.success,
                            size: 48,
                          ),
                        ),
                      ),
                      AppSpacing.gapVMd,

                      // 2. Headline
                      Text(
                        'Appointment Confirmed!',
                        style: AppTypography.headlineSm.copyWith(
                          color: AppColors.onSurface,
                          fontWeight: FontWeight.w800,
                          letterSpacing: -0.5,
                        ),
                        textAlign: TextAlign.center,
                      ),
                      AppSpacing.gapVXs,
                      Text(
                        'Your healthcare consultation is confirmed and scheduled.',
                        style: AppTypography.bodySm.copyWith(color: AppColors.onSurfaceVariant),
                        textAlign: TextAlign.center,
                      ),
                      AppSpacing.gapVLg,

                      // 3. Appointment Pass Card
                      AppCard(
                        padding: const EdgeInsets.all(AppSpacing.lg),
                        child: Column(
                          children: [
                            Row(
                              children: [
                                AppAvatar(
                                  name: appointment.doctorName,
                                  imageUrl: appointment.doctorAvatarUrl,
                                  size: 54,
                                ),
                                AppSpacing.gapHMd,
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Text(
                                        appointment.doctorName,
                                        style: AppTypography.titleMedium.copyWith(
                                          color: AppColors.onSurface,
                                          fontWeight: FontWeight.w700,
                                        ),
                                      ),
                                      AppSpacing.gapVXs,
                                      Text(
                                        appointment.doctorSpecialty,
                                        style: AppTypography.bodySm.copyWith(
                                          color: AppColors.primary,
                                          fontWeight: FontWeight.w600,
                                        ),
                                      ),
                                      Text(
                                        appointment.clinicName,
                                        style: AppTypography.labelSm.copyWith(color: AppColors.onSurfaceVariant),
                                        maxLines: 1,
                                        overflow: TextOverflow.ellipsis,
                                      ),
                                    ],
                                  ),
                                ),
                                const AppBadge(
                                  text: 'PAID & CONFIRMED',
                                  variant: BadgeVariant.success,
                                ),
                              ],
                            ),
                            const Divider(color: AppColors.outlineVariant, height: 28),
                            _SuccessDetailRow(
                              icon: Icons.qr_code_rounded,
                              label: 'Booking Pass ID',
                              value: appointment.id,
                            ),
                            AppSpacing.gapVSm,
                            _SuccessDetailRow(
                              icon: Icons.calendar_today_rounded,
                              label: 'Date',
                              value: dateFormatted,
                            ),
                            AppSpacing.gapVSm,
                            _SuccessDetailRow(
                              icon: Icons.access_time_rounded,
                              label: 'Time Slot',
                              value: appointment.timeSlot,
                            ),
                            AppSpacing.gapVSm,
                            _SuccessDetailRow(
                              icon: appointment.consultationType.icon,
                              label: 'Consultation Mode',
                              value: appointment.consultationType.label,
                            ),
                          ],
                        ),
                      ),
                      AppSpacing.gapVLg,

                      // 4. Action Buttons
                      AppButton(
                        text: 'View Consultation Pass',
                        prefixIcon: Icons.receipt_long_rounded,
                        onPressed: () => context.pushReplacement(
                          AppRoutes.appointmentDetail,
                          extra: appointment,
                        ),
                      ),
                      AppSpacing.gapVSm,
                      AppButton(
                        text: 'Back to Dashboard',
                        variant: ButtonVariant.ghost,
                        onPressed: () => context.go(AppRoutes.home),
                      ),
                    ],
                  ),
                ),
              ),
            );
          },
        ),
      ),
    );
  }
}

class _SuccessDetailRow extends StatelessWidget {
  final IconData icon;
  final String label;
  final String value;

  const _SuccessDetailRow({
    required this.icon,
    required this.label,
    required this.value,
  });

  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        Icon(icon, size: 16, color: AppColors.primary),
        AppSpacing.gapHSm,
        Text(
          label,
          style: AppTypography.bodySm.copyWith(color: AppColors.onSurfaceVariant),
        ),
        const Spacer(),
        Flexible(
          child: Text(
            value,
            textAlign: TextAlign.end,
            style: AppTypography.bodySm.copyWith(
              color: AppColors.onSurface,
              fontWeight: FontWeight.w700,
            ),
          ),
        ),
      ],
    );
  }
}
