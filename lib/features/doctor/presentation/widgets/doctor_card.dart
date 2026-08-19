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
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Doctor Info Header
          Row(
            crossAxisAlignment: CrossAxisAlignment.start,
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
                      style: AppTypography.titleLg.copyWith(
                        color: AppColors.onSurface,
                        fontWeight: FontWeight.w700,
                      ),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                    AppSpacing.gapVXs,
                    Text(
                      doctor.specialty,
                      style: AppTypography.bodySm.copyWith(
                        color: AppColors.primary,
                        fontWeight: FontWeight.w600,
                      ),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                    AppSpacing.gapVXs,
                    Text(
                      doctor.clinicName,
                      style: AppTypography.bodySm.copyWith(
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

          // Stats Chips Row
          Container(
            padding: const EdgeInsets.symmetric(horizontal: AppSpacing.md, vertical: AppSpacing.sm),
            decoration: BoxDecoration(
              color: AppColors.surfaceContainerLow,
              borderRadius: AppRadius.radiusMd,
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                // Rating
                Row(
                  children: [
                    const Icon(Icons.star_rounded, size: 18, color: Color(0xFFFFB300)),
                    AppSpacing.gapHXs,
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
                Container(width: 1, height: 16, color: AppColors.outlineVariant),

                // Experience
                Row(
                  children: [
                    const Icon(Icons.work_outline_rounded, size: 16, color: AppColors.primary),
                    AppSpacing.gapHXs,
                    Text(
                      '${doctor.experienceYears} yrs exp',
                      style: AppTypography.labelMd.copyWith(color: AppColors.onSurfaceVariant),
                    ),
                  ],
                ),
                Container(width: 1, height: 16, color: AppColors.outlineVariant),

                // Fee
                Text(
                  '\$${doctor.consultationFee.toStringAsFixed(0)}',
                  style: AppTypography.titleMd.copyWith(
                    color: AppColors.primary,
                    fontWeight: FontWeight.w700,
                  ),
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
                  text: 'Next: Tomorrow',
                  variant: BadgeVariant.secondary,
                  icon: Icons.calendar_today_rounded,
                ),
              InkWell(
                onTap: onBookPressed ?? () => context.push(AppRoutes.doctorDetail, extra: doctor),
                borderRadius: AppRadius.radiusBase,
                child: Padding(
                  padding: const EdgeInsets.symmetric(horizontal: AppSpacing.sm, vertical: AppSpacing.xs),
                  child: Row(
                    children: [
                      Text(
                        'Book Visit',
                        style: AppTypography.labelMd.copyWith(
                          color: AppColors.primary,
                          fontWeight: FontWeight.w700,
                        ),
                      ),
                      const Icon(Icons.chevron_right_rounded, size: 18, color: AppColors.primary),
                    ],
                  ),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }
}
