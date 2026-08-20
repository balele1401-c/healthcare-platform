import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_radius.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_typography.dart';
import '../../../../shared/widgets/app_badge.dart';
import '../../../../shared/widgets/app_card.dart';
import '../../../../shared/widgets/app_snackbar.dart';
import '../../domain/models/medical_record_model.dart';

class MedicalRecordDetailScreen extends StatelessWidget {
  final MedicalRecordModel record;

  const MedicalRecordDetailScreen({
    super.key,
    required this.record,
  });

  @override
  Widget build(BuildContext context) {
    final dateFormatted = DateFormat('EEEE, MMMM d, yyyy').format(record.visitDate);

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
          'Clinical EHR Record',
          style: AppTypography.titleLarge.copyWith(
            color: AppColors.onSurface,
            fontWeight: FontWeight.w800,
          ),
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.print_outlined, color: AppColors.primary),
            onPressed: () => AppSnackbar.showInfo(context, 'Preparing EHR medical summary PDF export...'),
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
                    // 1. Record ID & Doctor Banner
                    AppCard(
                      padding: const EdgeInsets.all(AppSpacing.lg),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Text(
                                'EHR DOCUMENT #${record.id}',
                                style: AppTypography.titleMedium.copyWith(
                                  color: AppColors.primary,
                                  fontWeight: FontWeight.w800,
                                  letterSpacing: 0.5,
                                ),
                              ),
                              const AppBadge(
                                text: 'VERIFIED EHR RECORD',
                                variant: BadgeVariant.success,
                              ),
                            ],
                          ),
                          AppSpacing.gapVMd,
                          Text(
                            record.diagnosis,
                            style: AppTypography.headlineSm.copyWith(
                              color: AppColors.onSurface,
                              fontWeight: FontWeight.w800,
                            ),
                          ),
                          AppSpacing.gapVSm,
                          Text(
                            dateFormatted,
                            style: AppTypography.bodySm.copyWith(
                              color: AppColors.onSurfaceVariant,
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                          AppSpacing.gapVMd,
                          Container(
                            padding: const EdgeInsets.all(AppSpacing.md),
                            decoration: BoxDecoration(
                              color: AppColors.surfaceContainerLow,
                              borderRadius: AppRadius.radiusMd,
                              border: Border.all(color: AppColors.outlineVariant, width: 0.8),
                            ),
                            child: Row(
                              children: [
                                const Icon(Icons.medical_services_outlined, color: AppColors.primary, size: 20),
                                AppSpacing.gapHMd,
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Text(
                                        'Attending: ${record.doctorName}',
                                        style: AppTypography.bodySm.copyWith(
                                          color: AppColors.onSurface,
                                          fontWeight: FontWeight.w700,
                                        ),
                                      ),
                                      Text(
                                        '${record.doctorSpecialty} • ${record.clinicName}',
                                        style: AppTypography.labelSm.copyWith(color: AppColors.onSurfaceVariant),
                                      ),
                                    ],
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ],
                      ),
                    ),
                    AppSpacing.gapVLg,

                    // 2. Chief Complaint & Symptoms
                    Text(
                      'Chief Complaint & Symptoms',
                      style: AppTypography.titleLarge.copyWith(
                        color: AppColors.onSurface,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                    AppSpacing.gapVSm,
                    AppCard(
                      padding: const EdgeInsets.all(AppSpacing.md),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            'REASON FOR CONSULTATION',
                            style: AppTypography.labelSm.copyWith(
                              color: AppColors.onSurfaceVariant,
                              fontWeight: FontWeight.w700,
                            ),
                          ),
                          AppSpacing.gapVXs,
                          Text(
                            record.chiefComplaint,
                            style: AppTypography.bodyMd.copyWith(color: AppColors.onSurface),
                          ),
                          AppSpacing.gapVMd,
                          Text(
                            'REPORTED SYMPTOMS',
                            style: AppTypography.labelSm.copyWith(
                              color: AppColors.onSurfaceVariant,
                              fontWeight: FontWeight.w700,
                            ),
                          ),
                          AppSpacing.gapVSm,
                          Wrap(
                            spacing: AppSpacing.sm,
                            runSpacing: AppSpacing.sm,
                            children: record.symptoms.map((s) {
                              return Container(
                                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                                decoration: BoxDecoration(
                                  color: AppColors.surfaceContainerLow,
                                  borderRadius: AppRadius.radiusFull,
                                  border: Border.all(color: AppColors.outlineVariant, width: 0.8),
                                ),
                                child: Text(
                                  s,
                                  style: AppTypography.labelSm.copyWith(
                                    color: AppColors.onSurface,
                                    fontWeight: FontWeight.w600,
                                  ),
                                ),
                              );
                            }).toList(),
                          ),
                        ],
                      ),
                    ),
                    AppSpacing.gapVLg,

                    // 3. Clinical Treatment & Instructions
                    Text(
                      'Physician Clinical Instructions & Plan',
                      style: AppTypography.titleLarge.copyWith(
                        color: AppColors.onSurface,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                    AppSpacing.gapVSm,
                    AppCard(
                      padding: const EdgeInsets.all(AppSpacing.md),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            'TREATMENT PLAN',
                            style: AppTypography.labelSm.copyWith(
                              color: AppColors.primary,
                              fontWeight: FontWeight.w700,
                            ),
                          ),
                          AppSpacing.gapVXs,
                          Text(
                            record.treatmentPlan,
                            style: AppTypography.bodyMd.copyWith(color: AppColors.onSurface),
                          ),
                          AppSpacing.gapVMd,
                          Text(
                            'FOLLOW-UP INSTRUCTIONS',
                            style: AppTypography.labelSm.copyWith(
                              color: AppColors.onSurfaceVariant,
                              fontWeight: FontWeight.w700,
                            ),
                          ),
                          AppSpacing.gapVXs,
                          Text(
                            record.followUpInstructions,
                            style: AppTypography.bodyMd.copyWith(color: AppColors.onSurface),
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
    );
  }
}
