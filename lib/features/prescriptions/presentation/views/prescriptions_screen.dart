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
import '../../../../shared/widgets/app_loading.dart';
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
          leading: IconButton(
            icon: const Icon(Icons.arrow_back_rounded, color: AppColors.onSurface),
            onPressed: () => Navigator.pop(context),
          ),
          title: Text(
            'Prescriptions & Medications',
            style: AppTypography.titleLarge.copyWith(
              color: AppColors.onSurface,
              fontWeight: FontWeight.w700,
            ),
          ),
          bottom: TabBar(
            indicatorColor: AppColors.primary,
            indicatorWeight: 3,
            labelColor: AppColors.primary,
            unselectedLabelColor: AppColors.onSurfaceVariant,
            labelStyle: AppTypography.titleMedium.copyWith(fontWeight: FontWeight.w700),
            unselectedLabelStyle: AppTypography.titleMedium.copyWith(fontWeight: FontWeight.w500),
            tabs: const [
              Tab(text: 'Active'),
              Tab(text: 'Completed'),
              Tab(text: 'Expired'),
            ],
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

    return prescriptionsAsync.when(
      data: (prescriptions) {
        if (prescriptions.isEmpty) {
          return AppEmptyState(
            icon: Icons.medication_outlined,
            title: 'No ${status.label} Prescriptions',
            message: 'You have no ${status.label.toLowerCase()} digital medication orders.',
          );
        }

        return RefreshIndicator(
          onRefresh: () async {
            ref.invalidate(activePrescriptionsProvider);
            ref.invalidate(completedPrescriptionsProvider);
            ref.invalidate(expiredPrescriptionsProvider);
          },
          child: ListView.separated(
            padding: const EdgeInsets.all(AppSpacing.marginMobile),
            itemCount: prescriptions.length,
            separatorBuilder: (_, __) => AppSpacing.gapVMd,
            itemBuilder: (context, index) {
              final rx = prescriptions[index];
              final dateFormatted = DateFormat('MMM d, yyyy').format(rx.issuedDate);
              final validUntilFormatted = DateFormat('MMM d, yyyy').format(rx.validUntil);

              return AppCard(
                onTap: () => context.push(AppRoutes.prescriptionDetail, extra: rx),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Row(
                          children: [
                            Container(
                              padding: const EdgeInsets.all(AppSpacing.xs),
                              decoration: BoxDecoration(
                                color: AppColors.primaryFixed,
                                borderRadius: AppRadius.radiusSm,
                              ),
                              child: const Icon(Icons.medication_outlined, size: 20, color: AppColors.primary),
                            ),
                            AppSpacing.gapHSm,
                            Text(
                              rx.id,
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
                          padding: const EdgeInsets.only(bottom: AppSpacing.xs),
                          child: Row(
                            children: [
                              const Icon(Icons.check_circle_outline_rounded, size: 16, color: AppColors.primary),
                              AppSpacing.gapHSm,
                              Expanded(
                                child: Text(
                                  '${item.medicineName} ${item.dosage}',
                                  style: AppTypography.bodyMd.copyWith(
                                    color: AppColors.onSurface,
                                    fontWeight: FontWeight.w600,
                                  ),
                                ),
                              ),
                            ],
                          ),
                        );
                      }).toList(),
                    ),
                    AppSpacing.gapVSm,
                    Text(
                      'Prescribing Doctor: ${rx.doctorName} (${rx.doctorSpecialty})',
                      style: AppTypography.bodySm.copyWith(color: AppColors.onSurfaceVariant),
                    ),
                    AppSpacing.gapVSm,
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text(
                          'Issued: $dateFormatted • Valid until: $validUntilFormatted',
                          style: AppTypography.labelSm.copyWith(color: AppColors.onSurfaceVariant),
                        ),
                        const Icon(Icons.chevron_right_rounded, size: 20, color: AppColors.outline),
                      ],
                    ),
                  ],
                ),
              );
            },
          ),
        );
      },
      loading: () => const Center(child: AppLoading(message: 'Loading prescriptions...')),
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
  }
}
