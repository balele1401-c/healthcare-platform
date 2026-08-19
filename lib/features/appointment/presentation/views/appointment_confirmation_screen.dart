import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';
import '../../../../core/routing/app_routes.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_typography.dart';
import '../../../../shared/widgets/app_avatar.dart';
import '../../../../shared/widgets/app_button.dart';
import '../../../../shared/widgets/app_card.dart';
import '../../../../shared/widgets/app_divider.dart';
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
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_rounded, color: AppColors.onSurface),
          onPressed: () => Navigator.pop(context),
        ),
        title: Text(
          'Confirm Appointment',
          style: AppTypography.titleLarge.copyWith(
            color: AppColors.onSurface,
            fontWeight: FontWeight.w700,
          ),
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.symmetric(
          horizontal: AppSpacing.marginMobile,
          vertical: AppSpacing.md,
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // 1. Doctor Profile Card
            AppCard(
              child: Row(
                children: [
                  AppAvatar(
                    name: doctor.name,
                    imageUrl: doctor.avatarUrl,
                    size: 60,
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
                        Text(
                          doctor.specialty,
                          style: AppTypography.bodySm.copyWith(
                            color: AppColors.primary,
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                        AppSpacing.gapVXs,
                        Text(
                          doctor.clinicName,
                          style: AppTypography.labelSm.copyWith(color: AppColors.onSurfaceVariant),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
            AppSpacing.gapVLg,

            // 2. Booking Schedule Details
            Text(
              'Schedule Details',
              style: AppTypography.headlineSm.copyWith(
                color: AppColors.onSurface,
                fontWeight: FontWeight.w700,
              ),
            ),
            AppSpacing.gapVSm,
            AppCard(
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
                    title: 'Consultation Mode',
                    value: draft.consultationType.label,
                  ),
                  if (draft.patientNotes != null && draft.patientNotes!.isNotEmpty) ...[
                    const Divider(color: AppColors.outlineVariant, height: 20),
                    _SummaryRow(
                      icon: Icons.note_alt_outlined,
                      title: 'Reason / Notes',
                      value: draft.patientNotes!,
                    ),
                  ],
                ],
              ),
            ),
            AppSpacing.gapVLg,

            // 3. Patient Information
            Text(
              'Patient Information',
              style: AppTypography.headlineSm.copyWith(
                color: AppColors.onSurface,
                fontWeight: FontWeight.w700,
              ),
            ),
            AppSpacing.gapVSm,
            AppCard(
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
                    title: 'Email Address',
                    value: profile.email,
                  ),
                ],
              ),
            ),
            AppSpacing.gapVLg,

            // 4. Payment Breakdown
            Text(
              'Fee Summary',
              style: AppTypography.headlineSm.copyWith(
                color: AppColors.onSurface,
                fontWeight: FontWeight.w700,
              ),
            ),
            AppSpacing.gapVSm,
            AppCard(
              child: Column(
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(
                        'Consultation Fee',
                        style: AppTypography.bodyMd.copyWith(color: AppColors.onSurfaceVariant),
                      ),
                      Text(
                        '\$${doctor.consultationFee.toStringAsFixed(2)}',
                        style: AppTypography.bodyMd.copyWith(
                          color: AppColors.onSurface,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                    ],
                  ),
                  AppSpacing.gapVSm,
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(
                        'Platform Service Fee',
                        style: AppTypography.bodyMd.copyWith(color: AppColors.onSurfaceVariant),
                      ),
                      Text(
                        '\$${serviceFee.toStringAsFixed(2)}',
                        style: AppTypography.bodyMd.copyWith(
                          color: AppColors.onSurface,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                    ],
                  ),
                  const AppDivider(),
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
                        style: AppTypography.headlineSm.copyWith(
                          color: AppColors.primary,
                          fontWeight: FontWeight.w800,
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
            AppSpacing.gapV2Xl,
          ],
        ),
      ),
      bottomNavigationBar: Container(
        padding: const EdgeInsets.symmetric(
          horizontal: AppSpacing.marginMobile,
          vertical: AppSpacing.md,
        ),
        decoration: BoxDecoration(
          color: AppColors.surface,
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.05),
              offset: const Offset(0, -4),
              blurRadius: 10,
            ),
          ],
        ),
        child: SafeArea(
          child: AppButton(
            text: 'Proceed to Payment (\$${totalAmount.toStringAsFixed(2)})',
            prefixIcon: Icons.lock_outline_rounded,
            onPressed: () => context.push(AppRoutes.payment),
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
        Icon(icon, size: 20, color: AppColors.primary),
        AppSpacing.gapHMd,
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                title,
                style: AppTypography.labelSm.copyWith(color: AppColors.onSurfaceVariant),
              ),
              AppSpacing.gapVXs,
              Text(
                value,
                style: AppTypography.bodyMd.copyWith(
                  color: AppColors.onSurface,
                  fontWeight: FontWeight.w600,
                ),
              ),
            ],
          ),
        ),
      ],
    );
  }
}
