import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';
import '../../../../core/routing/app_routes.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_shadows.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_typography.dart';
import '../../../../shared/widgets/app_avatar.dart';
import '../../../../shared/widgets/app_badge.dart';
import '../../../../shared/widgets/app_button.dart';
import '../../../../shared/widgets/app_card.dart';
import '../../../profile/presentation/controllers/profile_controller.dart';
import '../controllers/appointment_controller.dart';

class AppointmentConfirmationScreen extends ConsumerWidget {
  const AppointmentConfirmationScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final draft = ref.watch(bookingDraftProvider);
    final profile = ref.watch(patientProfileProvider);
    final doctor = draft.doctor;

    if (doctor == null) {
      return Scaffold(
        appBar: AppBar(title: const Text('Confirm Appointment')),
        body: Center(
          child: AppButton(
            text: 'Return to Home',
            onPressed: () => context.go(AppRoutes.home),
          ),
        ),
      );
    }

    final dateString = draft.selectedDate != null
        ? DateFormat('EEEE, MMMM d, yyyy').format(draft.selectedDate!)
        : 'Tomorrow';

    const double serviceFee = 5.0;
    final double totalAmount = doctor.consultationFee + serviceFee;

    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        backgroundColor: AppColors.surface,
        elevation: 0,
        scrolledUnderElevation: 0.5,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_rounded, color: AppColors.onSurface),
          onPressed: () => Navigator.pop(context),
        ),
        title: Text(
          'Review & Confirm',
          style: AppTypography.titleLarge.copyWith(
            color: AppColors.onSurface,
            fontWeight: FontWeight.w800,
          ),
        ),
      ),
      body: LayoutBuilder(
        builder: (context, constraints) {
          final isDesktop = constraints.maxWidth >= 900;

          return SingleChildScrollView(
            padding: EdgeInsets.symmetric(
              horizontal: isDesktop ? AppSpacing.desktopMargin : AppSpacing.marginMobile,
              vertical: AppSpacing.lg,
            ),
            child: Center(
              child: ConstrainedBox(
                constraints: const BoxConstraints(maxWidth: 860),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // 1. Doctor Profile Card
                    AppCard(
                      padding: const EdgeInsets.all(AppSpacing.md),
                      child: Row(
                        children: [
                          AppAvatar(
                            name: doctor.name,
                            imageUrl: doctor.avatarUrl,
                            size: 56,
                          ),
                          AppSpacing.gapHMd,
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  doctor.name,
                                  style: AppTypography.titleMedium.copyWith(
                                    color: AppColors.onSurface,
                                    fontWeight: FontWeight.w700,
                                  ),
                                ),
                                AppSpacing.gapVXs,
                                Text(
                                  doctor.specialty,
                                  style: AppTypography.bodySm.copyWith(
                                    color: AppColors.primary,
                                    fontWeight: FontWeight.w600,
                                  ),
                                ),
                                Text(
                                  doctor.clinicName,
                                  style: AppTypography.labelSm.copyWith(color: AppColors.onSurfaceVariant),
                                  maxLines: 1,
                                  overflow: TextOverflow.ellipsis,
                                ),
                              ],
                            ),
                          ),
                          const AppBadge(
                            text: 'Verified Doctor',
                            variant: BadgeVariant.success,
                          ),
                        ],
                      ),
                    ),
                    AppSpacing.gapVLg,

                    // 2. Booking Schedule Details
                    Text(
                      'Consultation Schedule',
                      style: AppTypography.titleLarge.copyWith(
                        color: AppColors.onSurface,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                    AppSpacing.gapVSm,
                    AppCard(
                      padding: const EdgeInsets.all(AppSpacing.md),
                      child: Column(
                        children: [
                          _SummaryRow(
                            icon: Icons.calendar_today_rounded,
                            title: 'Date',
                            value: dateString,
                          ),
                          const Divider(color: AppColors.outlineVariant, height: 20),
                          _SummaryRow(
                            icon: Icons.access_time_rounded,
                            title: 'Time Slot',
                            value: draft.selectedTimeSlot ?? '10:00 AM',
                          ),
                          const Divider(color: AppColors.outlineVariant, height: 20),
                          _SummaryRow(
                            icon: draft.consultationType.icon,
                            title: 'Mode',
                            value: draft.consultationType.label,
                          ),
                          if (draft.patientNotes != null && draft.patientNotes!.isNotEmpty) ...[
                            const Divider(color: AppColors.outlineVariant, height: 20),
                            _SummaryRow(
                              icon: Icons.notes_rounded,
                              title: 'Clinical Notes',
                              value: draft.patientNotes!,
                            ),
                          ],
                        ],
                      ),
                    ),
                    AppSpacing.gapVLg,

                    // 3. Patient Info Card
                    Text(
                      'Patient Details',
                      style: AppTypography.titleLarge.copyWith(
                        color: AppColors.onSurface,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                    AppSpacing.gapVSm,
                    AppCard(
                      padding: const EdgeInsets.all(AppSpacing.md),
                      child: Column(
                        children: [
                          _SummaryRow(
                            icon: Icons.person_outline_rounded,
                            title: 'Full Name',
                            value: profile.fullName,
                          ),
                          const Divider(color: AppColors.outlineVariant, height: 20),
                          _SummaryRow(
                            icon: Icons.phone_outlined,
                            title: 'Phone Number',
                            value: profile.phoneNumber,
                          ),
                          const Divider(color: AppColors.outlineVariant, height: 20),
                          _SummaryRow(
                            icon: Icons.email_outlined,
                            title: 'Email',
                            value: profile.email,
                          ),
                        ],
                      ),
                    ),
                    AppSpacing.gapVLg,

                    // 4. Payment Fee Breakdown
                    Text(
                      'Payment Summary',
                      style: AppTypography.titleLarge.copyWith(
                        color: AppColors.onSurface,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                    AppSpacing.gapVSm,
                    AppCard(
                      padding: const EdgeInsets.all(AppSpacing.md),
                      child: Column(
                        children: [
                          _FeeRow(
                            label: 'Specialist Consultation Fee',
                            amount: '\$${doctor.consultationFee.toStringAsFixed(2)}',
                          ),
                          AppSpacing.gapVSm,
                          const _FeeRow(
                            label: 'Platform & EHR Service Fee',
                            amount: '\$5.00',
                          ),
                          AppSpacing.gapVMd,
                          const Divider(color: AppColors.outlineVariant),
                          AppSpacing.gapVSm,
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Text(
                                'Total Payable',
                                style: AppTypography.titleMedium.copyWith(
                                  color: AppColors.onSurface,
                                  fontWeight: FontWeight.w800,
                                ),
                              ),
                              Text(
                                '\$${totalAmount.toStringAsFixed(2)}',
                                style: AppTypography.titleLarge.copyWith(
                                  color: AppColors.primary,
                                  fontWeight: FontWeight.w800,
                                ),
                              ),
                            ],
                          ),
                        ],
                      ),
                    ),
                    AppSpacing.gapVXxl,
                  ],
                ),
              ),
            ),
          );
        },
      ),

      // 5. Fixed Bottom Checkout Bar
      bottomNavigationBar: Container(
        padding: const EdgeInsets.symmetric(
          horizontal: AppSpacing.marginMobile,
          vertical: AppSpacing.md,
        ),
        decoration: const BoxDecoration(
          color: AppColors.surface,
          border: Border(
            top: BorderSide(color: AppColors.outlineVariant, width: 0.8),
          ),
          boxShadow: AppShadows.bottomNav,
        ),
        child: SafeArea(
          child: Center(
            child: ConstrainedBox(
              constraints: const BoxConstraints(maxWidth: 860),
              child: Row(
                children: [
                  Column(
                    mainAxisSize: MainAxisSize.min,
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'Total Payable',
                        style: AppTypography.labelSm.copyWith(color: AppColors.onSurfaceVariant),
                      ),
                      Text(
                        '\$${totalAmount.toStringAsFixed(2)}',
                        style: AppTypography.headlineSm.copyWith(
                          color: AppColors.onSurface,
                          fontWeight: FontWeight.w800,
                        ),
                      ),
                    ],
                  ),
                  AppSpacing.gapHLg,
                  Expanded(
                    child: AppButton(
                      text: 'Proceed to Secure Payment',
                      prefixIcon: Icons.lock_outline_rounded,
                      onPressed: () {
                        context.push(AppRoutes.payment);
                      },
                    ),
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}

class _SummaryRow extends StatelessWidget {
  final IconData icon;
  final String title;
  final String value;

  const _SummaryRow({
    required this.icon,
    required this.title,
    required this.value,
  });

  @override
  Widget build(BuildContext context) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Icon(icon, size: 18, color: AppColors.primary),
        AppSpacing.gapHSm,
        Text(
          title,
          style: AppTypography.bodySm.copyWith(color: AppColors.onSurfaceVariant),
        ),
        const Spacer(),
        Flexible(
          child: Text(
            value,
            textAlign: TextAlign.end,
            style: AppTypography.bodySm.copyWith(
              color: AppColors.onSurface,
              fontWeight: FontWeight.w600,
            ),
          ),
        ),
      ],
    );
  }
}

class _FeeRow extends StatelessWidget {
  final String label;
  final String amount;

  const _FeeRow({required this.label, required this.amount});

  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(
          label,
          style: AppTypography.bodySm.copyWith(color: AppColors.onSurfaceVariant),
        ),
        Text(
          amount,
          style: AppTypography.bodySm.copyWith(
            color: AppColors.onSurface,
            fontWeight: FontWeight.w600,
          ),
        ),
      ],
    );
  }
}
