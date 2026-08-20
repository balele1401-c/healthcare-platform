import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/routing/app_routes.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_radius.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_typography.dart';
import '../../../../shared/widgets/app_badge.dart';
import '../../../../shared/widgets/app_error.dart';
import '../../../../shared/widgets/app_metric_card.dart';
import '../../../../shared/widgets/app_skeleton.dart';
import '../../domain/models/health_metric_model.dart';
import '../controllers/health_tracker_controller.dart';

class HealthTrackerScreen extends ConsumerWidget {
  const HealthTrackerScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final metricsAsync = ref.watch(healthMetricsProvider);

    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        backgroundColor: AppColors.surface,
        elevation: 0,
        scrolledUnderElevation: 0.5,
        leading: Navigator.canPop(context)
            ? IconButton(
                icon: const Icon(Icons.arrow_back_rounded, color: AppColors.onSurface),
                onPressed: () => Navigator.pop(context),
              )
            : null,
        title: Text(
          'Vital Signs & Biomarkers',
          style: AppTypography.titleLarge.copyWith(
            color: AppColors.onSurface,
            fontWeight: FontWeight.w800,
          ),
        ),
      ),
      body: RefreshIndicator(
        onRefresh: () async => ref.invalidate(healthMetricsProvider),
        child: LayoutBuilder(
          builder: (context, constraints) {
            final isDesktop = constraints.maxWidth >= 900;
            final isTablet = constraints.maxWidth >= 600 && constraints.maxWidth < 900;

            return SingleChildScrollView(
              physics: const AlwaysScrollableScrollPhysics(),
              padding: EdgeInsets.symmetric(
                horizontal: isDesktop ? AppSpacing.desktopMargin : AppSpacing.marginMobile,
                vertical: AppSpacing.lg,
              ),
              child: Center(
                child: ConstrainedBox(
                  constraints: const BoxConstraints(maxWidth: 960),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // 1. Clinical Disclaimer
                      Container(
                        padding: const EdgeInsets.all(AppSpacing.md),
                        decoration: BoxDecoration(
                          color: AppColors.surfaceContainerLow,
                          borderRadius: AppRadius.radiusMd,
                          border: Border.all(color: AppColors.outlineVariant, width: 0.8),
                        ),
                        child: Row(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Icon(Icons.shield_outlined, color: AppColors.primary, size: 20),
                            AppSpacing.gapHMd,
                            Expanded(
                              child: Text(
                                'Health Tracker records patient biometric data and vital sign trends. It is designed to assist your healthcare provider. In an acute emergency, please contact 911 immediately.',
                                style: AppTypography.bodySm.copyWith(
                                  color: AppColors.onSurfaceVariant,
                                  height: 1.4,
                                ),
                              ),
                            ),
                          ],
                        ),
                      ),
                      AppSpacing.gapVLg,

                      // 2. Metrics Grid
                      Text(
                        'Continuous Vital Signs',
                        style: AppTypography.titleLarge.copyWith(
                          color: AppColors.onSurface,
                          fontWeight: FontWeight.w800,
                        ),
                      ),
                      AppSpacing.gapVSm,
                      metricsAsync.when(
                        data: (metrics) {
                          int crossAxisCount = 2;
                          double ratio = 1.15;
                          if (isDesktop) {
                            crossAxisCount = 3;
                            ratio = 1.35;
                          } else if (isTablet) {
                            crossAxisCount = 3;
                            ratio = 1.25;
                          }

                          return GridView.builder(
                            shrinkWrap: true,
                            physics: const NeverScrollableScrollPhysics(),
                            gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
                              crossAxisCount: crossAxisCount,
                              crossAxisSpacing: AppSpacing.md,
                              mainAxisSpacing: AppSpacing.md,
                              childAspectRatio: ratio,
                            ),
                            itemCount: metrics.length,
                            itemBuilder: (context, index) {
                              final metric = metrics[index];
                              Color accentColor = metric.type.color;
                              BadgeVariant variant = BadgeVariant.success;

                              if (metric.type == HealthMetricType.heartRate) {
                                variant = BadgeVariant.error;
                              } else if (metric.type == HealthMetricType.bloodPressure) {
                                variant = BadgeVariant.primary;
                              } else if (metric.type == HealthMetricType.steps) {
                                variant = BadgeVariant.secondary;
                              } else if (metric.type == HealthMetricType.bloodGlucose) {
                                variant = BadgeVariant.warning;
                              }

                              return AppMetricCard(
                                title: metric.type.displayName,
                                value: metric.currentValue,
                                unit: metric.type.unit,
                                icon: metric.type.icon,
                                accentColor: accentColor,
                                status: metric.statusLabel,
                                statusVariant: variant,
                                trend: metric.trend,
                                onTap: () => context.push(AppRoutes.healthMetricDetail, extra: metric),
                              );
                            },
                          );
                        },
                        loading: () => GridView.count(
                          crossAxisCount: isDesktop ? 3 : 2,
                          crossAxisSpacing: AppSpacing.md,
                          mainAxisSpacing: AppSpacing.md,
                          shrinkWrap: true,
                          physics: const NeverScrollableScrollPhysics(),
                          childAspectRatio: 1.35,
                          children: List.generate(
                            6,
                            (index) => const AppSkeleton(width: double.infinity, height: 140),
                          ),
                        ),
                        error: (err, _) => Center(
                          child: AppError(
                            message: 'Failed to load health metrics.',
                            onRetry: () => ref.invalidate(healthMetricsProvider),
                          ),
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
      ),
    );
  }
}
