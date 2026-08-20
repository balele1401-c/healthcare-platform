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
        scrolledUnderElevation: 0.5,
        leading: Navigator.canPop(context)
            ? IconButton(
                icon: const Icon(Icons.arrow_back_rounded, color: AppColors.onSurface),
                onPressed: () => Navigator.pop(context),
              )
            : null,
        title: Text(
          'Electronic Health Records',
          style: AppTypography.titleLarge.copyWith(
            color: AppColors.onSurface,
            fontWeight: FontWeight.w800,
          ),
        ),
      ),
      body: LayoutBuilder(
        builder: (context, constraints) {
          final isDesktop = constraints.maxWidth >= 900;

          return Column(
            children: [
              // 1. Search Bar
              Container(
                color: AppColors.surface,
                padding: EdgeInsets.symmetric(
                  horizontal: isDesktop ? AppSpacing.desktopMargin : AppSpacing.marginMobile,
                  vertical: AppSpacing.sm + 2,
                ),
                child: Center(
                  child: ConstrainedBox(
                    constraints: const BoxConstraints(maxWidth: 860),
                    child: TextField(
                      onChanged: (val) {
                        setState(() {
                          _searchQuery = val.toLowerCase().trim();
                        });
                      },
                      decoration: InputDecoration(
                        hintText: 'Search records by diagnosis, doctor name, symptoms...',
                        prefixIcon: const Icon(Icons.search_rounded, color: AppColors.onSurfaceVariant),
                        filled: true,
                        fillColor: AppColors.surfaceContainerLow,
                        contentPadding: const EdgeInsets.symmetric(horizontal: AppSpacing.md, vertical: 12),
                        border: OutlineInputBorder(
                          borderRadius: AppRadius.radiusMd,
                          borderSide: const BorderSide(color: AppColors.outlineVariant, width: 0.8),
                        ),
                        enabledBorder: OutlineInputBorder(
                          borderRadius: AppRadius.radiusMd,
                          borderSide: const BorderSide(color: AppColors.outlineVariant, width: 0.8),
                        ),
                        focusedBorder: OutlineInputBorder(
                          borderRadius: AppRadius.radiusMd,
                          borderSide: const BorderSide(color: AppColors.primary, width: 1.5),
                        ),
                      ),
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
                      return const Center(
                        child: AppEmptyState(
                          icon: Icons.folder_open_rounded,
                          title: 'No Health Records Found',
                          message: 'No clinical records matching your search query.',
                        ),
                      );
                    }

                    return RefreshIndicator(
                      onRefresh: () async => ref.invalidate(medicalRecordsProvider),
                      child: Center(
                        child: ConstrainedBox(
                          constraints: const BoxConstraints(maxWidth: 860),
                          child: ListView.separated(
                            padding: EdgeInsets.symmetric(
                              horizontal: isDesktop ? AppSpacing.desktopMargin : AppSpacing.marginMobile,
                              vertical: AppSpacing.lg,
                            ),
                            itemCount: filtered.length,
                            separatorBuilder: (context, index) => AppSpacing.gapVMd,
                            itemBuilder: (context, index) {
                              final record = filtered[index];
                              final dateFormatted = DateFormat('MMMM d, yyyy').format(record.visitDate);

                              return AppCard(
                                onTap: () => context.push(AppRoutes.medicalRecordDetail, extra: record),
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
                                          text: 'Verified EHR',
                                          variant: BadgeVariant.success,
                                          icon: Icons.verified_rounded,
                                        ),
                                      ],
                                    ),
                                    AppSpacing.gapVMd,

                                    Text(
                                      record.diagnosis,
                                      style: AppTypography.titleMedium.copyWith(
                                        color: AppColors.onSurface,
                                        fontWeight: FontWeight.w700,
                                      ),
                                    ),
                                    AppSpacing.gapVXs,
                                    Text(
                                      'Complaint: "${record.chiefComplaint}"',
                                      style: AppTypography.bodySm.copyWith(
                                        color: AppColors.onSurfaceVariant,
                                        fontStyle: FontStyle.italic,
                                      ),
                                      maxLines: 2,
                                      overflow: TextOverflow.ellipsis,
                                    ),
                                    AppSpacing.gapVMd,

                                    Container(
                                      padding: const EdgeInsets.symmetric(horizontal: AppSpacing.md, vertical: 8),
                                      decoration: BoxDecoration(
                                        color: AppColors.surfaceContainerLow,
                                        borderRadius: AppRadius.radiusMd,
                                        border: Border.all(color: AppColors.outlineVariant, width: 0.8),
                                      ),
                                      child: Row(
                                        children: [
                                          const Icon(Icons.person_pin_rounded, size: 16, color: AppColors.primary),
                                          AppSpacing.gapHSm,
                                          Text(
                                            '${record.doctorName} • ${record.doctorSpecialty}',
                                            style: AppTypography.labelMd.copyWith(
                                              color: AppColors.onSurfaceVariant,
                                              fontWeight: FontWeight.w600,
                                            ),
                                          ),
                                          const Spacer(),
                                          const Icon(Icons.chevron_right_rounded, size: 18, color: AppColors.outline),
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
                      message: 'Failed to load medical records.',
                      onRetry: () => ref.invalidate(medicalRecordsProvider),
                    ),
                  ),
                ),
              ),
            ],
          );
        },
      ),
    );
  }
}
