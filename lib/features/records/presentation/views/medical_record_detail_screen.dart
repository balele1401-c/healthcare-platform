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
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_rounded, color: AppColors.onSurface),
          onPressed: () => Navigator.pop(context),
        ),
        title: Text(
          'Clinical Record Details',
          style: AppTypography.titleLarge.copyWith(
            color: AppColors.onSurface,
            fontWeight: FontWeight.w700,
          ),
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.print_outlined, color: AppColors.primary),
            onPressed: () => AppSnackbar.showInfo(context, 'Preparing medical summary PDF export...'),
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
            // 1. Record ID & Doctor Banner
            AppCard(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(
                        record.id,
                        style: AppTypography.titleMedium.copyWith(
                          color: AppColors.primary,
                          fontWeight: FontWeight.w800,
                        ),
                      ),
                      const AppBadge(
                        text: 'Official EHR',
                        variant: BadgeVariant.success,
                      ),
                    ],
                  ),
                  AppSpacing.gapVSm,
                  Text(
                    dateFormatted,
                    style: AppTypography.titleLarge.copyWith(
                      color: AppColors.onSurface,
                      fontWeight: FontWeight.w700,
                    ),
                  ),
                  AppSpacing.gapVXs,
                  Text(
                    'Attending Physician: ${record.doctorName} (${record.doctorSpecialty})',
                    style: AppTypography.bodySm.copyWith(color: AppColors.onSurfaceVariant),
                  ),
                  Text(
                    'Facility: ${record.clinicName}',
                    style: AppTypography.bodySm.copyWith(color: AppColors.onSurfaceVariant),
                  ),
                ],
              ),
            ),
            AppSpacing.gapVLg,

            // 2. Chief Complaint & Symptoms
            Text(
              'Reason for Visit & Symptoms',
              style: AppTypography.headlineSm.copyWith(
                color: AppColors.onSurface,
                fontWeight: FontWeight.w700,
              ),
            ),
            AppSpacing.gapVSm,
            AppCard(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'Chief Complaint',
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
                    'Reported Symptoms',
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
                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                        decoration: BoxDecoration(
                          color: AppColors.surfaceContainerLow,
                          borderRadius: AppRadius.radiusFull,
                          border: Border.all(color: AppColors.outlineVariant),
                        ),
                        child: Text(
                          s,
                          style: AppTypography.bodySm.copyWith(
                            color: AppColors.onSurface,
                            fontWeight: FontWeight.w500,
                          ),
                        ),
                      );
                    }).toList(),
                  ),
                ],
              ),
            ),
            AppSpacing.gapVLg,

            // 3. Vital Signs Grid
            Text(
              'Vital Signs Recorded',
              style: AppTypography.headlineSm.copyWith(
                color: AppColors.onSurface,
                fontWeight: FontWeight.w700,
              ),
            ),
            AppSpacing.gapVSm,
            AppCard(
              child: GridView.count(
                crossAxisCount: 3,
                crossAxisSpacing: AppSpacing.sm,
                mainAxisSpacing: AppSpacing.md,
                shrinkWrap: true,
                physics: const NeverScrollableScrollPhysics(),
                childAspectRatio: 1.1,
                children: [
                  _VitalItem(label: 'Blood Pressure', value: record.vitals.bloodPressure, unit: 'mmHg', icon: Icons.speed_rounded),
                  _VitalItem(label: 'Heart Rate', value: '${record.vitals.heartRateBpm}', unit: 'bpm', icon: Icons.favorite_rounded, iconColor: AppColors.error),
                  _VitalItem(label: 'SpO2 Oxygen', value: '${record.vitals.oxygenSaturationPercent}', unit: '%', icon: Icons.air_rounded, iconColor: AppColors.primary),
                  _VitalItem(label: 'Body Temp', value: '${record.vitals.bodyTempCelsius}', unit: '°C', icon: Icons.thermostat_rounded),
                  _VitalItem(label: 'Weight', value: '${record.vitals.weightKg}', unit: 'kg', icon: Icons.monitor_weight_outlined),
                  _VitalItem(label: 'Height', value: '${record.vitals.heightCm.toInt()}', unit: 'cm', icon: Icons.height_rounded),
                ],
              ),
            ),
            AppSpacing.gapVLg,

            // 4. Clinical Diagnosis & ICD Assessment
            Text(
              'Clinical Diagnosis',
              style: AppTypography.headlineSm.copyWith(
                color: AppColors.onSurface,
                fontWeight: FontWeight.w700,
              ),
            ),
            AppSpacing.gapVSm,
            AppCard(
              child: Text(
                record.diagnosis,
                style: AppTypography.bodyMd.copyWith(
                  color: AppColors.onSurface,
                  fontWeight: FontWeight.w600,
                  height: 1.5,
                ),
              ),
            ),
            AppSpacing.gapVLg,

            // 5. Treatment Plan & Prescriptions
            Text(
              'Treatment Plan & Orders',
              style: AppTypography.headlineSm.copyWith(
                color: AppColors.onSurface,
                fontWeight: FontWeight.w700,
              ),
            ),
            AppSpacing.gapVSm,
            AppCard(
              child: Text(
                record.treatmentPlan,
                style: AppTypography.bodyMd.copyWith(
                  color: AppColors.onSurface,
                  height: 1.5,
                ),
              ),
            ),
            AppSpacing.gapVLg,

            // 6. Follow-up Instructions
            Text(
              'Follow-Up Instructions',
              style: AppTypography.headlineSm.copyWith(
                color: AppColors.onSurface,
                fontWeight: FontWeight.w700,
              ),
            ),
            AppSpacing.gapVSm,
            AppCard(
              child: Text(
                record.followUpInstructions,
                style: AppTypography.bodyMd.copyWith(
                  color: AppColors.onSurface,
                  height: 1.5,
                ),
              ),
            ),
            AppSpacing.gapVLg,

            // 7. Lab Result Attachments
            if (record.labResultAttachments.isNotEmpty) ...[
              Text(
                'Attached Diagnostic Reports',
                style: AppTypography.headlineSm.copyWith(
                  color: AppColors.onSurface,
                  fontWeight: FontWeight.w700,
                ),
              ),
              AppSpacing.gapVSm,
              Column(
                children: record.labResultAttachments.map((file) {
                  return Padding(
                    padding: const EdgeInsets.only(bottom: AppSpacing.sm),
                    child: AppCard(
                      onTap: () => AppSnackbar.showSuccess(context, 'Downloading $file...'),
                      padding: const EdgeInsets.all(AppSpacing.md),
                      child: Row(
                        children: [
                          Container(
                            padding: const EdgeInsets.all(AppSpacing.sm),
                            decoration: BoxDecoration(
                              color: const Color(0xFFFFEDEB),
                              borderRadius: AppRadius.radiusSm,
                            ),
                            child: const Icon(Icons.picture_as_pdf_rounded, color: AppColors.error, size: 22),
                          ),
                          AppSpacing.gapHMd,
                          Expanded(
                            child: Text(
                              file,
                              style: AppTypography.bodyMd.copyWith(
                                color: AppColors.onSurface,
                                fontWeight: FontWeight.w600,
                              ),
                            ),
                          ),
                          const Icon(Icons.download_rounded, color: AppColors.primary),
                        ],
                      ),
                    ),
                  );
                }).toList(),
              ),
            ],
            AppSpacing.gapV2Xl,
          ],
        ),
      ),
    );
  }
}

class _VitalItem extends StatelessWidget {
  final String label;
  final String value;
  final String unit;
  final IconData icon;
  final Color? iconColor;

  const _VitalItem({
    required this.label,
    required this.value,
    required this.unit,
    required this.icon,
    this.iconColor,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(AppSpacing.sm),
      decoration: BoxDecoration(
        color: AppColors.surfaceContainerLow,
        borderRadius: AppRadius.radiusMd,
      ),
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(icon, size: 18, color: iconColor ?? AppColors.primary),
          AppSpacing.gapVXs,
          Text(
            value,
            style: AppTypography.titleMedium.copyWith(
              color: AppColors.onSurface,
              fontWeight: FontWeight.w800,
            ),
          ),
          Text(
            unit,
            style: AppTypography.labelSm.copyWith(color: AppColors.onSurfaceVariant),
          ),
        ],
      ),
    );
  }
}
