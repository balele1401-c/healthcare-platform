import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';
import '../../../../core/routing/app_routes.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_radius.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_typography.dart';
import '../../../../shared/widgets/app_badge.dart';
import '../../../../shared/widgets/app_card.dart';
import '../../../../shared/widgets/app_empty_state.dart';
import '../../../../shared/widgets/app_error.dart';
import '../../../../shared/widgets/app_skeleton.dart';
import '../../domain/models/prescription_model.dart';
import '../controllers/prescription_controller.dart';

class PrescriptionsScreen extends ConsumerWidget {
  const PrescriptionsScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    return DefaultTabController(
      length: 3,
      child: Scaffold(
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
            'Prescriptions & Pharmacy',
            style: AppTypography.titleLarge.copyWith(
              color: AppColors.onSurface,
              fontWeight: FontWeight.w800,
            ),
          ),
          bottom: PreferredSize(
            preferredSize: const Size.fromHeight(48),
            child: Container(
              padding: const EdgeInsets.symmetric(horizontal: AppSpacing.md, vertical: 4),
              color: AppColors.surface,
              child: TabBar(
                indicatorColor: AppColors.primary,
                indicatorWeight: 3,
                labelColor: AppColors.primary,
                unselectedLabelColor: AppColors.onSurfaceVariant,
                labelStyle: AppTypography.titleMedium.copyWith(fontWeight: FontWeight.w700, fontSize: 14),
                unselectedLabelStyle: AppTypography.titleMedium.copyWith(fontWeight: FontWeight.w500, fontSize: 14),
                tabs: const [
                  Tab(text: 'Active Rx'),
                  Tab(text: 'Completed'),
                  Tab(text: 'Expired'),
                ],
              ),
            ),
          ),
        ),
        body: const TabBarView(
          children: [
            _PrescriptionTabList(status: PrescriptionStatus.active),
            _PrescriptionTabList(status: PrescriptionStatus.completed),
            _PrescriptionTabList(status: PrescriptionStatus.expired),
          ],
        ),
      ),
    );
  }
}

class _PrescriptionTabList extends ConsumerWidget {
  final PrescriptionStatus status;

  const _PrescriptionTabList({required this.status});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final prescriptionsAsync = status == PrescriptionStatus.active
        ? ref.watch(activePrescriptionsProvider)
        : status == PrescriptionStatus.completed
            ? ref.watch(completedPrescriptionsProvider)
            : ref.watch(expiredPrescriptionsProvider);

    return LayoutBuilder(
      builder: (context, constraints) {
        final isDesktop = constraints.maxWidth >= 900;

        return prescriptionsAsync.when(
          data: (prescriptions) {
            if (prescriptions.isEmpty) {
              return Center(
                child: AppEmptyState(
                  icon: Icons.medication_outlined,
                  title: 'No ${status.label} Prescriptions',
                  message: 'You have no ${status.label.toLowerCase()} digital medication orders on file.',
                ),
              );
            }

            return RefreshIndicator(
              onRefresh: () async {
                ref.invalidate(activePrescriptionsProvider);
                ref.invalidate(completedPrescriptionsProvider);
                ref.invalidate(expiredPrescriptionsProvider);
              },
              child: Center(
                child: ConstrainedBox(
                  constraints: const BoxConstraints(maxWidth: 860),
                  child: ListView.separated(
                    padding: EdgeInsets.symmetric(
                      horizontal: isDesktop ? AppSpacing.desktopMargin : AppSpacing.marginMobile,
                      vertical: AppSpacing.lg,
                    ),
                    itemCount: prescriptions.length,
                    separatorBuilder: (context, index) => AppSpacing.gapVMd,
                    itemBuilder: (context, index) {
                      final rx = prescriptions[index];
                      final validUntilFormatted = DateFormat('MMM d, yyyy').format(rx.validUntil);

                      return AppCard(
                        onTap: () => context.push(AppRoutes.prescriptionDetail, extra: rx),
                        padding: const EdgeInsets.all(AppSpacing.md),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                Row(
                                  children: [
                                    Container(
                                      padding: const EdgeInsets.all(6),
                                      decoration: BoxDecoration(
                                        color: AppColors.primaryContainer,
                                        borderRadius: AppRadius.radiusSm,
                                      ),
                                      child: const Icon(Icons.medication_outlined, size: 20, color: AppColors.primary),
                                    ),
                                    AppSpacing.gapHSm,
                                    Text(
                                      'Rx Order #${rx.id}',
                                      style: AppTypography.titleMedium.copyWith(
                                        color: AppColors.onSurface,
                                        fontWeight: FontWeight.w700,
                                      ),
                                    ),
                                  ],
                                ),
                                AppBadge(
                                  text: rx.status.label,
                                  variant: rx.status == PrescriptionStatus.active
                                      ? BadgeVariant.success
                                      : rx.status == PrescriptionStatus.completed
                                          ? BadgeVariant.primary
                                          : BadgeVariant.error,
                                ),
                              ],
                            ),
                            AppSpacing.gapVMd,

                            // Medicines summary
                            Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: rx.items.map((item) {
                                return Padding(
                                  padding: const EdgeInsets.only(bottom: 6),
                                  child: Row(
                                    children: [
                                      const Icon(Icons.check_circle_outline_rounded, size: 16, color: AppColors.secondary),
                                      AppSpacing.gapHSm,
                                      Expanded(
                                        child: Text(
                                          '${item.medicineName} ${item.dosage}',
                                          style: AppTypography.bodySm.copyWith(
                                            color: AppColors.onSurface,
                                            fontWeight: FontWeight.w600,
                                          ),
                                        ),
                                      ),
                                      Text(
                                        'Qty: ${item.totalQuantity}',
                                        style: AppTypography.labelSm.copyWith(
                                          color: AppColors.onSurfaceVariant,
                                        ),
                                      ),
                                    ],
                                  ),
                                );
                              }).toList(),
                            ),
                            AppSpacing.gapVMd,

                            // Footer info strip
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
                                  Text(
                                    'Dr. ${rx.doctorName}',
                                    style: AppTypography.labelSm.copyWith(
                                      color: AppColors.onSurface,
                                      fontWeight: FontWeight.w600,
                                    ),
                                  ),
                                  Text(
                                    'Valid until: $validUntilFormatted',
                                    style: AppTypography.labelSm.copyWith(
                                      color: AppColors.onSurfaceVariant,
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ],
                        ),
                      );
                    },
                  ),
                ),
              ),
            );
          },
          loading: () => Center(
            child: ConstrainedBox(
              constraints: const BoxConstraints(maxWidth: 860),
              child: ListView.separated(
                padding: const EdgeInsets.all(AppSpacing.marginMobile),
                itemCount: 3,
                separatorBuilder: (context, index) => AppSpacing.gapVMd,
                itemBuilder: (context, index) => const AppSkeleton(width: double.infinity, height: 160),
              ),
            ),
          ),
          error: (err, _) => Center(
            child: AppError(
              message: 'Failed to load prescriptions.',
              onRetry: () {
                ref.invalidate(activePrescriptionsProvider);
                ref.invalidate(completedPrescriptionsProvider);
                ref.invalidate(expiredPrescriptionsProvider);
              },
            ),
          ),
        );
      },
    );
  }
}
