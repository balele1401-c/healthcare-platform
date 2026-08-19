import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/routing/app_routes.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_radius.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_typography.dart';
import '../../../../shared/widgets/app_avatar.dart';
import '../../../../shared/widgets/app_button.dart';
import '../../../../shared/widgets/app_card.dart';
import '../../../appointment/presentation/controllers/appointment_controller.dart';
import '../../domain/models/doctor_model.dart';

class DoctorDetailScreen extends ConsumerWidget {
  final DoctorModel doctor;

  const DoctorDetailScreen({
    super.key,
    required this.doctor,
  });

  @override
  Widget build(BuildContext context, WidgetRef ref) {
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
          'Doctor Profile',
          style: AppTypography.titleLarge.copyWith(
            color: AppColors.onSurface,
            fontWeight: FontWeight.w700,
          ),
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.share_outlined, color: AppColors.onSurface),
            onPressed: () {},
          ),
        ],
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.symmetric(
          horizontal: AppSpacing.marginMobile,
          vertical: AppSpacing.md,
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // 1. Doctor Header Card
            AppCard(
              child: Column(
                children: [
                  Center(
                    child: AppAvatar(
                      name: doctor.name,
                      imageUrl: doctor.avatarUrl,
                      size: 90,
                    ),
                  ),
                  AppSpacing.gapVMd,
                  Text(
                    doctor.name,
                    style: AppTypography.headlineSm.copyWith(
                      color: AppColors.onSurface,
                      fontWeight: FontWeight.w700,
                    ),
                    textAlign: TextAlign.center,
                  ),
                  AppSpacing.gapVXs,
                  Text(
                    doctor.specialty,
                    style: AppTypography.titleMedium.copyWith(
                      color: AppColors.primary,
                      fontWeight: FontWeight.w600,
                    ),
                    textAlign: TextAlign.center,
                  ),
                  AppSpacing.gapVXs,
                  Text(
                    doctor.title,
                    style: AppTypography.bodySm.copyWith(color: AppColors.onSurfaceVariant),
                    textAlign: TextAlign.center,
                  ),
                  AppSpacing.gapVMd,

                  // Key Metrics Strip (Patients, Experience, Rating, Reviews)
                  Container(
                    padding: const EdgeInsets.symmetric(vertical: AppSpacing.md, horizontal: AppSpacing.sm),
                    decoration: BoxDecoration(
                      color: AppColors.surfaceContainerLow,
                      borderRadius: AppRadius.radiusMd,
                    ),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceAround,
                      children: [
                        _DetailStat(
                          icon: Icons.people_outline_rounded,
                          value: '${doctor.patientCount}+',
                          label: 'Patients',
                        ),
                        Container(width: 1, height: 28, color: AppColors.outlineVariant),
                        _DetailStat(
                          icon: Icons.work_outline_rounded,
                          value: '${doctor.experienceYears} Yrs',
                          label: 'Experience',
                        ),
                        Container(width: 1, height: 28, color: AppColors.outlineVariant),
                        _DetailStat(
                          icon: Icons.star_rounded,
                          value: '${doctor.rating}',
                          label: '${doctor.reviewCount} Reviews',
                          iconColor: const Color(0xFFFFB300),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
            AppSpacing.gapVLg,

            // 2. About Doctor
            Text(
              'About Doctor',
              style: AppTypography.headlineSm.copyWith(
                color: AppColors.onSurface,
                fontWeight: FontWeight.w700,
              ),
            ),
            AppSpacing.gapVSm,
            AppCard(
              child: Text(
                doctor.biography,
                style: AppTypography.bodyMd.copyWith(
                  color: AppColors.onSurfaceVariant,
                  height: 1.5,
                ),
              ),
            ),
            AppSpacing.gapVLg,

            // 3. Education & Credentials
            Text(
              'Education & Credentials',
              style: AppTypography.headlineSm.copyWith(
                color: AppColors.onSurface,
                fontWeight: FontWeight.w700,
              ),
            ),
            AppSpacing.gapVSm,
            AppCard(
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Container(
                    padding: const EdgeInsets.all(AppSpacing.sm),
                    decoration: BoxDecoration(
                      color: AppColors.primaryFixed,
                      borderRadius: AppRadius.radiusSm,
                    ),
                    child: const Icon(Icons.school_outlined, color: AppColors.primary, size: 22),
                  ),
                  AppSpacing.gapHMd,
                  Expanded(
                    child: Text(
                      doctor.education,
                      style: AppTypography.bodyMd.copyWith(
                        color: AppColors.onSurface,
                        fontWeight: FontWeight.w500,
                      ),
                    ),
                  ),
                ],
              ),
            ),
            AppSpacing.gapVLg,

            // 4. Practice Location / Clinic
            Text(
              'Practice Location',
              style: AppTypography.headlineSm.copyWith(
                color: AppColors.onSurface,
                fontWeight: FontWeight.w700,
              ),
            ),
            AppSpacing.gapVSm,
            AppCard(
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Container(
                    padding: const EdgeInsets.all(AppSpacing.sm),
                    decoration: BoxDecoration(
                      color: AppColors.secondaryContainer,
                      borderRadius: AppRadius.radiusSm,
                    ),
                    child: const Icon(Icons.location_on_outlined, color: AppColors.secondary, size: 22),
                  ),
                  AppSpacing.gapHMd,
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          doctor.clinicName,
                          style: AppTypography.titleMedium.copyWith(
                            color: AppColors.onSurface,
                            fontWeight: FontWeight.w700,
                          ),
                        ),
                        AppSpacing.gapVXs,
                        Text(
                          doctor.clinicAddress,
                          style: AppTypography.bodySm.copyWith(color: AppColors.onSurfaceVariant),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
            AppSpacing.gapV2Xl,
          ],
        ),
      ),

      // 5. Fixed Bottom Booking Bar
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
          child: Row(
            children: [
              Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'Consultation Fee',
                    style: AppTypography.labelSm.copyWith(color: AppColors.onSurfaceVariant),
                  ),
                  Text(
                    '\$${doctor.consultationFee.toStringAsFixed(0)}',
                    style: AppTypography.headlineSm.copyWith(
                      color: AppColors.primary,
                      fontWeight: FontWeight.w800,
                    ),
                  ),
                ],
              ),
              AppSpacing.gapHLg,
              Expanded(
                child: AppButton(
                  text: 'Select Date & Time',
                  prefixIcon: Icons.calendar_today_rounded,
                  onPressed: () {
                    ref.read(bookingDraftProvider.notifier).setDoctor(doctor);
                    context.push(AppRoutes.selectDateTime, extra: doctor);
                  },
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _DetailStat extends StatelessWidget {
  final IconData icon;
  final String value;
  final String label;
  final Color? iconColor;

  const _DetailStat({
    required this.icon,
    required this.value,
    required this.label,
    this.iconColor,
  });

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        Icon(icon, size: 20, color: iconColor ?? AppColors.primary),
        AppSpacing.gapVXs,
        Text(
          value,
          style: AppTypography.titleMedium.copyWith(
            color: AppColors.onSurface,
            fontWeight: FontWeight.w700,
          ),
        ),
        Text(
          label,
          style: AppTypography.labelSm.copyWith(color: AppColors.onSurfaceVariant),
        ),
      ],
    );
  }
}
