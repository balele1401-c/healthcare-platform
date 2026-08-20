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
        scrolledUnderElevation: 0.5,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_rounded, color: AppColors.onSurface),
          onPressed: () => Navigator.pop(context),
        ),
        title: Text(
          'Digital Rx Order',
          style: AppTypography.titleLarge.copyWith(
            color: AppColors.onSurface,
            fontWeight: FontWeight.w800,
          ),
        ),
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
                    // 1. Header Card
                    AppCard(
                      padding: const EdgeInsets.all(AppSpacing.lg),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Text(
                                'RX ORDER #${prescription.id}',
                                style: AppTypography.titleMedium.copyWith(
                                  color: AppColors.primary,
                                  fontWeight: FontWeight.w800,
                                  letterSpacing: 0.5,
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
                            'Prescribed by Dr. ${prescription.doctorName}',
                            style: AppTypography.titleLarge.copyWith(
                              color: AppColors.onSurface,
                              fontWeight: FontWeight.w800,
                            ),
                          ),
                          Text(
                            prescription.doctorSpecialty,
                            style: AppTypography.bodySm.copyWith(
                              color: AppColors.primary,
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                          const Divider(color: AppColors.outlineVariant, height: 24),
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text('Issued Date', style: AppTypography.labelSm.copyWith(color: AppColors.onSurfaceVariant)),
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
                      'Prescribed Medications (${prescription.items.length})',
                      style: AppTypography.titleLarge.copyWith(
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
                            padding: const EdgeInsets.all(AppSpacing.md),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Row(
                                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                  children: [
                                    Expanded(
                                      child: Text(
                                        item.medicineName,
                                        style: AppTypography.titleMedium.copyWith(
                                          color: AppColors.onSurface,
                                          fontWeight: FontWeight.w700,
                                          fontSize: 16,
                                        ),
                                      ),
                                    ),
                                    AppBadge(
                                      text: item.dosage,
                                      variant: BadgeVariant.secondary,
                                    ),
                                  ],
                                ),
                                AppSpacing.gapVSm,
                                Row(
                                  children: [
                                    const Icon(Icons.schedule_rounded, size: 16, color: AppColors.primary),
                                    AppSpacing.gapHSm,
                                    Text(
                                      'Frequency: ${item.frequency}',
                                      style: AppTypography.bodySm.copyWith(
                                        color: AppColors.onSurface,
                                        fontWeight: FontWeight.w600,
                                      ),
                                    ),
                                    const Spacer(),
                                    Text(
                                      'Qty: ${item.totalQuantity}',
                                      style: AppTypography.bodySm.copyWith(
                                        color: AppColors.onSurfaceVariant,
                                        fontWeight: FontWeight.w600,
                                      ),
                                    ),
                                  ],
                                ),
                                if (item.instructions.isNotEmpty) ...[
                                  AppSpacing.gapVSm,
                                  Container(
                                    width: double.infinity,
                                    padding: const EdgeInsets.all(AppSpacing.sm),
                                    decoration: BoxDecoration(
                                      color: AppColors.surfaceContainerLow,
                                      borderRadius: AppRadius.radiusSm,
                                    ),
                                    child: Text(
                                      'Note: ${item.instructions}',
                                      style: AppTypography.labelSm.copyWith(color: AppColors.onSurfaceVariant),
                                    ),
                                  ),
                                ],
                              ],
                            ),
                          ),
                        );
                      }).toList(),
                    ),
                    AppSpacing.gapVLg,

                    // 3. Pharmacy Actions
                    if (prescription.status == PrescriptionStatus.active) ...[
                      AppButton(
                        text: 'Order Pharmacy Delivery',
                        prefixIcon: Icons.local_shipping_outlined,
                        onPressed: () {
                          AppSnackbar.showSuccess(
                            context,
                            'Medication delivery order submitted to partner pharmacy.',
                          );
                        },
                      ),
                      AppSpacing.gapVSm,
                      AppButton(
                        text: 'Request Prescription Refill',
                        variant: ButtonVariant.outlined,
                        prefixIcon: Icons.refresh_rounded,
                        onPressed: () {
                          AppSnackbar.showSuccess(
                            context,
                            'Refill authorization requested from Dr. ${prescription.doctorName}.',
                          );
                        },
                      ),
                    ],
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
