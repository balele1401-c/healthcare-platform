import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_radius.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_typography.dart';
import '../../../../shared/widgets/app_badge.dart';
import '../../../../shared/widgets/app_button.dart';
import '../../../../shared/widgets/app_card.dart';
import '../../../../shared/widgets/app_snackbar.dart';
import '../../domain/models/prescription_model.dart';

class PrescriptionDetailScreen extends StatelessWidget {
  final PrescriptionModel prescription;

  const PrescriptionDetailScreen({
    super.key,
    required this.prescription,
  });

  @override
  Widget build(BuildContext context) {
    final issuedFormatted = DateFormat('MMMM d, yyyy').format(prescription.issuedDate);
    final validUntilFormatted = DateFormat('MMMM d, yyyy').format(prescription.validUntil);

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
          'Prescription Details',
          style: AppTypography.titleLarge.copyWith(
            color: AppColors.onSurface,
            fontWeight: FontWeight.w700,
          ),
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.symmetric(
          horizontal: AppSpacing.marginMobile,
          vertical: AppSpacing.md,
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // 1. Header Card
            AppCard(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(
                        prescription.id,
                        style: AppTypography.titleMedium.copyWith(
                          color: AppColors.primary,
                          fontWeight: FontWeight.w800,
                        ),
                      ),
                      AppBadge(
                        text: prescription.status.label,
                        variant: prescription.status == PrescriptionStatus.active
                            ? BadgeVariant.success
                            : prescription.status == PrescriptionStatus.completed
                                ? BadgeVariant.primary
                                : BadgeVariant.error,
                      ),
                    ],
                  ),
                  AppSpacing.gapVMd,
                  Text(
                    'Prescribed by ${prescription.doctorName}',
                    style: AppTypography.titleLarge.copyWith(
                      color: AppColors.onSurface,
                      fontWeight: FontWeight.w700,
                    ),
                  ),
                  Text(
                    prescription.doctorSpecialty,
                    style: AppTypography.bodySm.copyWith(color: AppColors.primary),
                  ),
                  const Divider(color: AppColors.outlineVariant, height: 24),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text('Date Issued', style: AppTypography.labelSm.copyWith(color: AppColors.onSurfaceVariant)),
                          AppSpacing.gapVXs,
                          Text(issuedFormatted, style: AppTypography.bodySm.copyWith(fontWeight: FontWeight.w600)),
                        ],
                      ),
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.end,
                        children: [
                          Text('Valid Until', style: AppTypography.labelSm.copyWith(color: AppColors.onSurfaceVariant)),
                          AppSpacing.gapVXs,
                          Text(validUntilFormatted, style: AppTypography.bodySm.copyWith(fontWeight: FontWeight.w600)),
                        ],
                      ),
                    ],
                  ),
                ],
              ),
            ),
            AppSpacing.gapVLg,

            // 2. Medication Items
            Text(
              'Medication Orders (${prescription.items.length})',
              style: AppTypography.headlineSm.copyWith(
                color: AppColors.onSurface,
                fontWeight: FontWeight.w700,
              ),
            ),
            AppSpacing.gapVSm,
            Column(
              children: prescription.items.map((item) {
                return Padding(
                  padding: const EdgeInsets.only(bottom: AppSpacing.md),
                  child: AppCard(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Text(
                              item.medicineName,
                              style: AppTypography.titleMedium.copyWith(
                                color: AppColors.onSurface,
                                fontWeight: FontWeight.w700,
                              ),
                            ),
                            Container(
                              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                              decoration: BoxDecoration(
                                color: AppColors.primaryFixed,
                                borderRadius: AppRadius.radiusSm,
                              ),
                              child: Text(
                                item.dosage,
                                style: AppTypography.labelSm.copyWith(
                                  color: AppColors.primary,
                                  fontWeight: FontWeight.w700,
                                ),
                              ),
                            ),
                          ],
                        ),
                        AppSpacing.gapVSm,
                        _MedDetailRow(label: 'Formulation', value: item.form),
                        _MedDetailRow(label: 'Dosage Schedule', value: item.frequency),
                        _MedDetailRow(label: 'Duration', value: item.duration),
                        _MedDetailRow(label: 'Total Quantity', value: '${item.totalQuantity} units'),
                        _MedDetailRow(label: 'Refills Remaining', value: '${item.refillsRemaining} remaining'),
                        const Divider(color: AppColors.outlineVariant, height: 16),
                        Text(
                          'Instructions',
                          style: AppTypography.labelSm.copyWith(
                            color: AppColors.onSurfaceVariant,
                            fontWeight: FontWeight.w700,
                          ),
                        ),
                        AppSpacing.gapVXs,
                        Text(
                          item.instructions,
                          style: AppTypography.bodyMd.copyWith(color: AppColors.onSurface),
                        ),
                      ],
                    ),
                  ),
                );
              }).toList(),
            ),
            AppSpacing.gapVLg,

            // 3. Clinical Instructions & Notes
            Text(
              'Doctor Notes & Warnings',
              style: AppTypography.headlineSm.copyWith(
                color: AppColors.onSurface,
                fontWeight: FontWeight.w700,
              ),
            ),
            AppSpacing.gapVSm,
            AppCard(
              child: Text(
                prescription.notes,
                style: AppTypography.bodyMd.copyWith(
                  color: AppColors.onSurface,
                  height: 1.5,
                ),
              ),
            ),
            AppSpacing.gapV2Xl,
          ],
        ),
      ),
      bottomNavigationBar: prescription.status == PrescriptionStatus.active
          ? Container(
              padding: const EdgeInsets.symmetric(
                horizontal: AppSpacing.marginMobile,
                vertical: AppSpacing.md,
              ),
              decoration: BoxDecoration(
                color: AppColors.surface,
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.05),
                    offset: const Offset(0, -4),
                    blurRadius: 10,
                  ),
                ],
              ),
              child: SafeArea(
                child: AppButton(
                  text: 'Request Pharmacy Refill',
                  prefixIcon: Icons.local_pharmacy_outlined,
                  onPressed: () {
                    AppSnackbar.showSuccess(
                      context,
                      'Refill request submitted to affiliated pharmacy.',
                    );
                  },
                ),
              ),
            )
          : null,
    );
  }
}

class _MedDetailRow extends StatelessWidget {
  final String label;
  final String value;

  const _MedDetailRow({required this.label, required this.value});

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 6),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label, style: AppTypography.bodySm.copyWith(color: AppColors.onSurfaceVariant)),
          Text(value, style: AppTypography.bodySm.copyWith(color: AppColors.onSurface, fontWeight: FontWeight.w600)),
        ],
      ),
    );
  }
}
