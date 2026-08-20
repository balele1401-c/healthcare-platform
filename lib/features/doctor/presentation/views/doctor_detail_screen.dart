import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/routing/app_routes.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_radius.dart';
import '../../../../core/theme/app_shadows.dart';
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
        scrolledUnderElevation: 0.5,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_rounded, color: AppColors.onSurface),
          onPressed: () => Navigator.pop(context),
        ),
        title: Text(
          'Specialist Profile',
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
                    // 1. Doctor Header Hero Card
                    AppCard(
                      padding: const EdgeInsets.all(AppSpacing.xl),
                      child: Column(
                        children: [
                          Center(
                            child: Stack(
                              children: [
                                AppAvatar(
                                  name: doctor.name,
                                  imageUrl: doctor.avatarUrl,
                                  size: 96,
                                ),
                                Positioned(
                                  bottom: 2,
                                  right: 2,
                                  child: Container(
                                    padding: const EdgeInsets.all(4),
                                    decoration: const BoxDecoration(
                                      color: Colors.white,
                                      shape: BoxShape.circle,
                                    ),
                                    child: const Icon(
                                      Icons.verified_rounded,
                                      size: 22,
                                      color: AppColors.secondary,
                                    ),
                                  ),
                                ),
                              ],
                            ),
                          ),
                          AppSpacing.gapVMd,
                          Text(
                            doctor.name,
                            style: AppTypography.headlineSm.copyWith(
                              color: AppColors.onSurface,
                              fontWeight: FontWeight.w800,
                            ),
                            textAlign: TextAlign.center,
                          ),
                          AppSpacing.gapVXs,
                          Text(
                            doctor.specialty,
                            style: AppTypography.titleMedium.copyWith(
                              color: AppColors.primary,
                              fontWeight: FontWeight.w700,
                            ),
                            textAlign: TextAlign.center,
                          ),
                          AppSpacing.gapVXs,
                          Text(
                            doctor.title,
                            style: AppTypography.bodySm.copyWith(color: AppColors.onSurfaceVariant),
                            textAlign: TextAlign.center,
                          ),
                          AppSpacing.gapVLg,

                          // Key Metrics Strip (Patients, Experience, Rating, Reviews)
                          Container(
                            padding: const EdgeInsets.symmetric(vertical: AppSpacing.md, horizontal: AppSpacing.md),
                            decoration: BoxDecoration(
                              color: AppColors.surfaceContainerLow,
                              borderRadius: AppRadius.radiusMd,
                              border: Border.all(color: AppColors.outlineVariant, width: 0.8),
                            ),
                            child: Row(
                              mainAxisAlignment: MainAxisAlignment.spaceAround,
                              children: [
                                _DetailStat(
                                  icon: Icons.people_alt_outlined,
                                  value: '${doctor.patientCount}+',
                                  label: 'Patients',
                                ),
                                Container(width: 1, height: 32, color: AppColors.outlineVariant),
                                _DetailStat(
                                  icon: Icons.medical_services_outlined,
                                  value: '${doctor.experienceYears} Yrs',
                                  label: 'Experience',
                                ),
                                Container(width: 1, height: 32, color: AppColors.outlineVariant),
                                _DetailStat(
                                  icon: Icons.star_rounded,
                                  value: '${doctor.rating}',
                                  label: '${doctor.reviewCount} Reviews',
                                  iconColor: const Color(0xFFF59E0B),
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
                      style: AppTypography.titleLarge.copyWith(
                        color: AppColors.onSurface,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                    AppSpacing.gapVSm,
                    AppCard(
                      padding: const EdgeInsets.all(AppSpacing.lg),
                      child: Text(
                        doctor.biography,
                        style: AppTypography.bodyMd.copyWith(
                          color: AppColors.onSurfaceVariant,
                          height: 1.6,
                        ),
                      ),
                    ),
                    AppSpacing.gapVLg,

                    // 3. Education & Credentials
                    Text(
                      'Education & Certifications',
                      style: AppTypography.titleLarge.copyWith(
                        color: AppColors.onSurface,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                    AppSpacing.gapVSm,
                    AppCard(
                      padding: const EdgeInsets.all(AppSpacing.md),
                      child: Row(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Container(
                            padding: const EdgeInsets.all(AppSpacing.sm),
                            decoration: BoxDecoration(
                              color: AppColors.primaryContainer,
                              borderRadius: AppRadius.radiusMd,
                            ),
                            child: const Icon(Icons.school_outlined, color: AppColors.primary, size: 22),
                          ),
                          AppSpacing.gapHMd,
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  'Medical Degree & Residency',
                                  style: AppTypography.titleMedium.copyWith(
                                    color: AppColors.onSurface,
                                    fontWeight: FontWeight.w700,
                                  ),
                                ),
                                AppSpacing.gapVXs,
                                Text(
                                  doctor.education,
                                  style: AppTypography.bodySm.copyWith(
                                    color: AppColors.onSurfaceVariant,
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ],
                      ),
                    ),
                    AppSpacing.gapVLg,

                    // 4. Practice Location / Clinic
                    Text(
                      'Clinic & Hospital Affiliation',
                      style: AppTypography.titleLarge.copyWith(
                        color: AppColors.onSurface,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                    AppSpacing.gapVSm,
                    AppCard(
                      padding: const EdgeInsets.all(AppSpacing.md),
                      child: Row(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Container(
                            padding: const EdgeInsets.all(AppSpacing.sm),
                            decoration: BoxDecoration(
                              color: AppColors.secondaryContainer,
                              borderRadius: AppRadius.radiusMd,
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
                    AppSpacing.gapVXxl,
                  ],
                ),
              ),
            ),
          );
        },
      ),

      // 5. Fixed Bottom Booking Bar
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
                        'Consultation Fee',
                        style: AppTypography.labelSm.copyWith(color: AppColors.onSurfaceVariant),
                      ),
                      Text(
                        '\$${doctor.consultationFee.toStringAsFixed(0)}',
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
                      text: 'Select Date & Time Slot',
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
        Icon(icon, size: 22, color: iconColor ?? AppColors.primary),
        AppSpacing.gapVXs,
        Text(
          value,
          style: AppTypography.titleMedium.copyWith(
            color: AppColors.onSurface,
            fontWeight: FontWeight.w800,
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
