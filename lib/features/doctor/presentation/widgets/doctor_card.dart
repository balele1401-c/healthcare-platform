import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/routing/app_routes.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_radius.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_typography.dart';
import '../../../../shared/widgets/app_avatar.dart';
import '../../../../shared/widgets/app_badge.dart';
import '../../../../shared/widgets/app_card.dart';
import '../../domain/models/doctor_model.dart';

class DoctorCard extends StatelessWidget {
  final DoctorModel doctor;
  final VoidCallback? onBookPressed;

  const DoctorCard({
    super.key,
    required this.doctor,
    this.onBookPressed,
  });

  @override
  Widget build(BuildContext context) {
    return AppCard(
      onTap: () => context.push(AppRoutes.doctorDetail, extra: doctor),
      padding: const EdgeInsets.all(AppSpacing.md),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Doctor Info Header
          Row(
            crossAxisAlignment: CrossAxisAlignment.center,
            children: [
              Stack(
                children: [
                  AppAvatar(
                    name: doctor.name,
                    imageUrl: doctor.avatarUrl,
                    size: 54,
                  ),
                  Positioned(
                    bottom: 0,
                    right: 0,
                    child: Container(
                      padding: const EdgeInsets.all(2),
                      decoration: const BoxDecoration(
                        color: Colors.white,
                        shape: BoxShape.circle,
                      ),
                      child: const Icon(
                        Icons.verified_rounded,
                        size: 16,
                        color: AppColors.secondary,
                      ),
                    ),
                  ),
                ],
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
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                    Text(
                      doctor.specialty,
                      style: AppTypography.bodySm.copyWith(
                        color: AppColors.primary,
                        fontWeight: FontWeight.w600,
                      ),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                    Text(
                      doctor.clinicName,
                      style: AppTypography.labelSm.copyWith(
                        color: AppColors.onSurfaceVariant,
                      ),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                  ],
                ),
              ),
            ],
          ),
          AppSpacing.gapVMd,

          // Stats Chips Row (Tonal Surface)
          Container(
            padding: const EdgeInsets.symmetric(horizontal: AppSpacing.md, vertical: 8),
            decoration: BoxDecoration(
              color: AppColors.surfaceContainerLow,
              borderRadius: AppRadius.radiusMd,
              border: Border.all(color: AppColors.outlineVariant, width: 0.8),
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                // Rating
                Row(
                  children: [
                    const Icon(Icons.star_rounded, size: 16, color: Color(0xFFF59E0B)),
                    const SizedBox(width: 4),
                    Text(
                      '${doctor.rating}',
                      style: AppTypography.labelMd.copyWith(
                        color: AppColors.onSurface,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                    Text(
                      ' (${doctor.reviewCount})',
                      style: AppTypography.labelSm.copyWith(color: AppColors.onSurfaceVariant),
                    ),
                  ],
                ),
                Container(width: 1, height: 14, color: AppColors.outlineVariant),

                // Experience
                Row(
                  children: [
                    const Icon(Icons.medical_services_outlined, size: 15, color: AppColors.onSurfaceVariant),
                    const SizedBox(width: 4),
                    Text(
                      '${doctor.experienceYears} yrs exp',
                      style: AppTypography.labelSm.copyWith(
                        color: AppColors.onSurfaceVariant,
                        fontWeight: FontWeight.w500,
                      ),
                    ),
                  ],
                ),
                Container(width: 1, height: 14, color: AppColors.outlineVariant),

                // Fee
                Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Text(
                      '\$${doctor.consultationFee.toStringAsFixed(0)}',
                      style: AppTypography.titleMedium.copyWith(
                        color: AppColors.onSurface,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                    Text(
                      ' / visit',
                      style: AppTypography.labelSm.copyWith(
                        color: AppColors.onSurfaceMuted,
                        fontWeight: FontWeight.w400,
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
          AppSpacing.gapVMd,

          // Footer Availability & Action
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              if (doctor.isAvailableToday)
                const AppBadge(
                  text: 'Available Today',
                  variant: BadgeVariant.success,
                  icon: Icons.check_circle_outline_rounded,
                )
              else
                const AppBadge(
                  text: 'Next Slot: Tomorrow',
                  variant: BadgeVariant.neutral,
                  icon: Icons.schedule_rounded,
                ),
              ElevatedButton(
                onPressed: onBookPressed ?? () => context.push(AppRoutes.doctorDetail, extra: doctor),
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppColors.primary,
                  foregroundColor: Colors.white,
                  elevation: 0,
                  padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                  shape: const RoundedRectangleBorder(
                    borderRadius: AppRadius.radiusBase,
                  ),
                  minimumSize: const Size(0, 34),
                ),
                child: Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Text(
                      'Book Visit',
                      style: AppTypography.labelMd.copyWith(
                        color: Colors.white,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                    const SizedBox(width: 2),
                    const Icon(Icons.arrow_forward_rounded, size: 14, color: Colors.white),
                  ],
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }
}
