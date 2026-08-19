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
import '../controllers/medical_record_controller.dart';

class MedicalRecordsScreen extends ConsumerStatefulWidget {
  const MedicalRecordsScreen({super.key});

  @override
  ConsumerState<MedicalRecordsScreen> createState() => _MedicalRecordsScreenState();
}

class _MedicalRecordsScreenState extends ConsumerState<MedicalRecordsScreen> {
  String _searchQuery = '';

  @override
  Widget build(BuildContext context) {
    final recordsAsync = ref.watch(medicalRecordsProvider);

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
          'Electronic Health Records',
          style: AppTypography.titleLarge.copyWith(
            color: AppColors.onSurface,
            fontWeight: FontWeight.w700,
          ),
        ),
      ),
      body: Column(
        children: [
          // 1. Search Bar
          Container(
            color: AppColors.surface,
            padding: const EdgeInsets.symmetric(
              horizontal: AppSpacing.marginMobile,
              vertical: AppSpacing.sm,
            ),
            child: TextField(
              onChanged: (val) {
                setState(() {
                  _searchQuery = val.toLowerCase().trim();
                });
              },
              decoration: InputDecoration(
                hintText: 'Search records by diagnosis, doctor...',
                prefixIcon: const Icon(Icons.search_rounded, color: AppColors.onSurfaceVariant),
                filled: true,
                fillColor: AppColors.surfaceContainerLow,
                contentPadding: const EdgeInsets.symmetric(horizontal: AppSpacing.md, vertical: 12),
                border: OutlineInputBorder(
                  borderRadius: AppRadius.radiusMd,
                  borderSide: const BorderSide(color: AppColors.outlineVariant),
                ),
                enabledBorder: OutlineInputBorder(
                  borderRadius: AppRadius.radiusMd,
                  borderSide: const BorderSide(color: AppColors.outlineVariant),
                ),
                focusedBorder: OutlineInputBorder(
                  borderRadius: AppRadius.radiusMd,
                  borderSide: const BorderSide(color: AppColors.primary, width: 2),
                ),
              ),
            ),
          ),
          const Divider(height: 1, color: AppColors.outlineVariant),

          // 2. Records List
          Expanded(
            child: recordsAsync.when(
              data: (records) {
                final filtered = records.where((r) {
                  if (_searchQuery.isEmpty) return true;
                  return r.doctorName.toLowerCase().contains(_searchQuery) ||
                      r.diagnosis.toLowerCase().contains(_searchQuery) ||
                      r.chiefComplaint.toLowerCase().contains(_searchQuery) ||
                      r.doctorSpecialty.toLowerCase().contains(_searchQuery);
                }).toList();

                if (filtered.isEmpty) {
                  return const AppEmptyState(
                    icon: Icons.folder_open_rounded,
                    title: 'No Health Records Found',
                    message: 'No clinical records matching your search query.',
                  );
                }

                return RefreshIndicator(
                  onRefresh: () async => ref.invalidate(medicalRecordsProvider),
                  child: ListView.separated(
                    padding: const EdgeInsets.all(AppSpacing.marginMobile),
                    itemCount: filtered.length,
                    separatorBuilder: (_, __) => AppSpacing.gapVMd,
                    itemBuilder: (context, index) {
                      final record = filtered[index];
                      final dateFormatted = DateFormat('MMM d, yyyy').format(record.visitDate);

                      return AppCard(
                        onTap: () => context.push(AppRoutes.medicalRecordDetail, extra: record),
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
                                      child: const Icon(
                                        Icons.description_outlined,
                                        size: 18,
                                        color: AppColors.primary,
                                      ),
                                    ),
                                    AppSpacing.gapHSm,
                                    Text(
                                      dateFormatted,
                                      style: AppTypography.titleMedium.copyWith(
                                        color: AppColors.onSurface,
                                        fontWeight: FontWeight.w700,
                                      ),
                                    ),
                                  ],
                                ),
                                const AppBadge(
                                  text: 'Verified Clinical Record',
                                  variant: BadgeVariant.success,
                                ),
                              ],
                            ),
                            AppSpacing.gapVMd,
                            Text(
                              record.diagnosis,
                              style: AppTypography.bodyMd.copyWith(
                                color: AppColors.onSurface,
                                fontWeight: FontWeight.w600,
                              ),
                              maxLines: 2,
                              overflow: TextOverflow.ellipsis,
                            ),
                            AppSpacing.gapVSm,
                            Text(
                              'Physician: ${record.doctorName} (${record.doctorSpecialty})',
                              style: AppTypography.bodySm.copyWith(color: AppColors.primary),
                            ),
                            Text(
                              record.clinicName,
                              style: AppTypography.labelSm.copyWith(color: AppColors.onSurfaceVariant),
                            ),
                            AppSpacing.gapVMd,
                            Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                Row(
                                  children: [
                                    const Icon(Icons.favorite_rounded, size: 14, color: AppColors.error),
                                    AppSpacing.gapHXs,
                                    Text(
                                      'BP: ${record.vitals.bloodPressure} • HR: ${record.vitals.heartRateBpm} bpm',
                                      style: AppTypography.labelSm.copyWith(color: AppColors.onSurfaceVariant),
                                    ),
                                  ],
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
              loading: () => const Center(child: AppLoading(message: 'Loading health records...')),
              error: (err, _) => Center(
                child: AppError(
                  message: 'Failed to load records.',
                  onRetry: () => ref.invalidate(medicalRecordsProvider),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
