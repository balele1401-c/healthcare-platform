import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';
import '../../../../core/routing/app_routes.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_typography.dart';
import '../../../../shared/widgets/app_avatar.dart';
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
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: AppSpacing.marginMobile),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Spacer(),

              // 1. Success Animated Icon Container
              Container(
                width: 96,
                height: 96,
                decoration: BoxDecoration(
                  color: AppColors.success.withOpacity(0.12),
                  shape: BoxShape.circle,
                ),
                child: Center(
                  child: Container(
                    width: 72,
                    height: 72,
                    decoration: const BoxDecoration(
                      color: AppColors.success,
                      shape: BoxShape.circle,
                    ),
                    child: const Icon(
                      Icons.check_rounded,
                      color: AppColors.onPrimary,
                      size: 44,
                    ),
                  ),
                ),
              ),
              AppSpacing.gapVLg,

              // 2. Headline
              Text(
                'Appointment Confirmed!',
                style: AppTypography.headlineLgMobile.copyWith(
                  color: AppColors.onSurface,
                  fontWeight: FontWeight.w800,
                ),
                textAlign: TextAlign.center,
              ),
              AppSpacing.gapVSm,
              Text(
                'Your consultation has been successfully booked and added to your calendar.',
                style: AppTypography.bodyMd.copyWith(color: AppColors.onSurfaceVariant),
                textAlign: TextAlign.center,
              ),
              AppSpacing.gapV2Xl,

              // 3. Appointment Summary Card
              AppCard(
                child: Column(
                  children: [
                    Row(
                      children: [
                        AppAvatar(
                          name: appointment.doctorName,
                          imageUrl: appointment.doctorAvatarUrl,
                          size: 50,
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
                              Text(
                                appointment.doctorSpecialty,
                                style: AppTypography.bodySm.copyWith(color: AppColors.primary),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                    const Divider(color: AppColors.outlineVariant, height: 24),
                    _SuccessDetailRow(
                      icon: Icons.confirmation_number_outlined,
                      label: 'Booking ID',
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
                      label: 'Time',
                      value: appointment.timeSlot,
                    ),
                    AppSpacing.gapVSm,
                    _SuccessDetailRow(
                      icon: appointment.consultationType.icon,
                      label: 'Mode',
                      value: appointment.consultationType.label,
                    ),
                  ],
                ),
              ),

              const Spacer(),

              // 4. Action Buttons
              AppButton(
                text: 'View Appointment Details',
                onPressed: () => context.pushReplacement(
                  AppRoutes.appointmentDetail,
                  extra: appointment,
                ),
              ),
              AppSpacing.gapVMd,
              AppButton(
                text: 'Back to Home',
                variant: ButtonVariant.outlined,
                onPressed: () => context.go(AppRoutes.home),
              ),
              AppSpacing.gapVLg,
            ],
          ),
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
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Row(
          children: [
            Icon(icon, size: 18, color: AppColors.onSurfaceVariant),
            AppSpacing.gapHSm,
            Text(
              label,
              style: AppTypography.bodySm.copyWith(color: AppColors.onSurfaceVariant),
            ),
          ],
        ),
        Text(
          value,
          style: AppTypography.bodySm.copyWith(
            color: AppColors.onSurface,
            fontWeight: FontWeight.w600,
          ),
        ),
      ],
    );
  }
}
