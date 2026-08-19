import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_radius.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_typography.dart';
import '../../../../shared/widgets/app_button.dart';
import '../../../../shared/widgets/app_card.dart';
import '../../../../shared/widgets/app_snackbar.dart';
import '../../domain/models/health_metric_model.dart';
import '../controllers/health_tracker_controller.dart';

class HealthMetricDetailScreen extends ConsumerStatefulWidget {
  final HealthMetricModel metric;

  const HealthMetricDetailScreen({
    super.key,
    required this.metric,
  });

  @override
  ConsumerState<HealthMetricDetailScreen> createState() => _HealthMetricDetailScreenState();
}

class _HealthMetricDetailScreenState extends ConsumerState<HealthMetricDetailScreen> {
  late HealthMetricModel _metric;

  @override
  void initState() {
    super.initState();
    _metric = widget.metric;
  }

  void _openAddReadingModal() {
    final valueController = TextEditingController();
    final noteController = TextEditingController();

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (ctx) => Container(
        decoration: const BoxDecoration(
          color: AppColors.surface,
          borderRadius: BorderRadius.vertical(top: Radius.circular(AppRadius.xxl)),
        ),
        padding: EdgeInsets.only(
          top: AppSpacing.lg,
          left: AppSpacing.lg,
          right: AppSpacing.lg,
          bottom: MediaQuery.of(ctx).viewInsets.bottom + AppSpacing.xl,
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Center(
              child: Container(
                width: 40,
                height: 4,
                decoration: BoxDecoration(
                  color: AppColors.outlineVariant,
                  borderRadius: AppRadius.radiusFull,
                ),
              ),
            ),
            AppSpacing.gapVMd,
            Text(
              'Log ${_metric.type.displayName}',
              style: AppTypography.headlineSm.copyWith(
                color: AppColors.onSurface,
                fontWeight: FontWeight.w700,
              ),
            ),
            AppSpacing.gapVSm,
            Text(
              'Enter your measured reading in ${_metric.type.unit}.',
              style: AppTypography.bodySm.copyWith(color: AppColors.onSurfaceVariant),
            ),
            AppSpacing.gapVLg,
            TextField(
              controller: valueController,
              keyboardType: const TextInputType.numberWithOptions(decimal: true),
              decoration: InputDecoration(
                labelText: 'Reading Value (${_metric.type.unit})',
                hintText: 'e.g. ${_metric.currentValue}',
                filled: true,
                fillColor: AppColors.surfaceContainerLow,
                border: OutlineInputBorder(borderRadius: AppRadius.radiusMd),
              ),
            ),
            AppSpacing.gapVMd,
            TextField(
              controller: noteController,
              decoration: InputDecoration(
                labelText: 'Clinical Note (Optional)',
                hintText: 'e.g. Resting morning reading',
                filled: true,
                fillColor: AppColors.surfaceContainerLow,
                border: OutlineInputBorder(borderRadius: AppRadius.radiusMd),
              ),
            ),
            AppSpacing.gapVLg,
            AppButton(
              text: 'Save Measurement',
              onPressed: () async {
                final text = valueController.text.trim();
                final val = double.tryParse(text);
                if (val == null || val <= 0) {
                  AppSnackbar.showError(ctx, 'Please enter a valid numeric reading.');
                  return;
                }

                final repo = ref.read(healthTrackerRepositoryProvider);
                await repo.addReading(_metric.type, val, note: noteController.text.trim());
                ref.invalidate(healthMetricsProvider);

                final updated = await repo.getMetricByType(_metric.type);
                if (updated != null && mounted) {
                  setState(() {
                    _metric = updated;
                  });
                }

                if (ctx.mounted) {
                  Navigator.pop(ctx);
                }
                if (mounted) {
                  AppSnackbar.showSuccess(context, 'Measurement logged successfully.');
                }
              },
            ),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final lastUpdatedFormatted = DateFormat('MMM d, h:mm a').format(_metric.lastUpdated);

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
          _metric.type.displayName,
          style: AppTypography.titleLarge.copyWith(
            color: AppColors.onSurface,
            fontWeight: FontWeight.w700,
          ),
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.add_rounded, color: AppColors.primary),
            onPressed: _openAddReadingModal,
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
            // 1. Current Metric Overview Card
            AppCard(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Row(
                        children: [
                          Container(
                            padding: const EdgeInsets.all(AppSpacing.sm),
                            decoration: BoxDecoration(
                              color: _metric.type.color.withOpacity(0.12),
                              borderRadius: AppRadius.radiusSm,
                            ),
                            child: Icon(_metric.type.icon, color: _metric.type.color, size: 24),
                          ),
                          AppSpacing.gapHMd,
                          Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                _metric.type.displayName,
                                style: AppTypography.titleMedium.copyWith(
                                  color: AppColors.onSurface,
                                  fontWeight: FontWeight.w700,
                                ),
                              ),
                              Text(
                                'Updated: $lastUpdatedFormatted',
                                style: AppTypography.labelSm.copyWith(color: AppColors.onSurfaceVariant),
                              ),
                            ],
                          ),
                        ],
                      ),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                        decoration: BoxDecoration(
                          color: AppColors.surfaceContainerLow,
                          borderRadius: AppRadius.radiusFull,
                        ),
                        child: Text(
                          _metric.statusLabel,
                          style: AppTypography.labelSm.copyWith(
                            color: _metric.type.color,
                            fontWeight: FontWeight.w700,
                          ),
                        ),
                      ),
                    ],
                  ),
                  AppSpacing.gapVLg,
                  Row(
                    crossAxisAlignment: CrossAxisAlignment.baseline,
                    textBaseline: TextBaseline.alphabetic,
                    children: [
                      Text(
                        _metric.currentValue,
                        style: AppTypography.displayLarge.copyWith(
                          color: AppColors.onSurface,
                          fontWeight: FontWeight.w800,
                        ),
                      ),
                      AppSpacing.gapHSm,
                      Text(
                        _metric.type.unit,
                        style: AppTypography.titleMedium.copyWith(color: AppColors.onSurfaceVariant),
                      ),
                    ],
                  ),
                  AppSpacing.gapVSm,
                  Text(
                    'Trend: ${_metric.trend}',
                    style: AppTypography.bodySm.copyWith(
                      color: AppColors.primary,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ],
              ),
            ),
            AppSpacing.gapVLg,

            // 2. Trend Visualizer Chart
            Text(
              'Weekly Trend',
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
                  SizedBox(
                    height: 140,
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                      crossAxisAlignment: CrossAxisAlignment.end,
                      children: _metric.history.take(7).toList().reversed.map((reading) {
                        final maxVal = _metric.history.map((r) => r.value).reduce((a, b) => a > b ? a : b);
                        final heightRatio = maxVal > 0 ? (reading.value / maxVal).clamp(0.2, 1.0) : 0.5;

                        return Column(
                          mainAxisAlignment: MainAxisAlignment.end,
                          children: [
                            Text(
                              reading.value == reading.value.roundToDouble()
                                  ? reading.value.toInt().toString()
                                  : reading.value.toStringAsFixed(1),
                              style: AppTypography.labelSm.copyWith(
                                fontSize: 10,
                                color: AppColors.onSurfaceVariant,
                              ),
                            ),
                            AppSpacing.gapVXs,
                            Container(
                              width: 24,
                              height: 90 * heightRatio,
                              decoration: BoxDecoration(
                                color: _metric.type.color.withOpacity(0.85),
                                borderRadius: const BorderRadius.vertical(top: Radius.circular(4)),
                              ),
                            ),
                            AppSpacing.gapVXs,
                            Text(
                              DateFormat('E').format(reading.timestamp),
                              style: AppTypography.labelSm.copyWith(
                                fontSize: 11,
                                color: AppColors.onSurfaceVariant,
                              ),
                            ),
                          ],
                        );
                      }).toList(),
                    ),
                  ),
                ],
              ),
            ),
            AppSpacing.gapVLg,

            // 3. Historical Measurements List
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  'Reading History (${_metric.history.length})',
                  style: AppTypography.headlineSm.copyWith(
                    color: AppColors.onSurface,
                    fontWeight: FontWeight.w700,
                  ),
                ),
                IconButton(
                  icon: const Icon(Icons.add_circle_outline_rounded, color: AppColors.primary),
                  onPressed: _openAddReadingModal,
                ),
              ],
            ),
            AppSpacing.gapVSm,
            Column(
              children: _metric.history.map((reading) {
                final timeFormatted = DateFormat('MMM d, yyyy • h:mm a').format(reading.timestamp);
                final valString = reading.secondaryValue != null
                    ? '${reading.value.toInt()}/${reading.secondaryValue}'
                    : reading.value == reading.value.roundToDouble()
                        ? reading.value.toInt().toString()
                        : reading.value.toStringAsFixed(1);

                return Padding(
                  padding: const EdgeInsets.only(bottom: AppSpacing.sm),
                  child: AppCard(
                    padding: const EdgeInsets.symmetric(horizontal: AppSpacing.md, vertical: AppSpacing.sm),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              timeFormatted,
                              style: AppTypography.labelSm.copyWith(color: AppColors.onSurfaceVariant),
                            ),
                            AppSpacing.gapVXs,
                            Text(
                              reading.note,
                              style: AppTypography.bodySm.copyWith(
                                color: AppColors.onSurface,
                                fontWeight: FontWeight.w500,
                              ),
                            ),
                          ],
                        ),
                        Text(
                          '$valString ${_metric.type.unit}',
                          style: AppTypography.titleMedium.copyWith(
                            color: AppColors.primary,
                            fontWeight: FontWeight.w800,
                          ),
                        ),
                      ],
                    ),
                  ),
                );
              }).toList(),
            ),
            AppSpacing.gapV2Xl,
          ],
        ),
      ),
      bottomNavigationBar: Container(
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
            text: 'Add New Measurement',
            prefixIcon: Icons.add_rounded,
            onPressed: _openAddReadingModal,
          ),
        ),
      ),
    );
  }
}
