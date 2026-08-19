import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_radius.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_typography.dart';
import '../../../../shared/widgets/app_button.dart';
import '../controllers/doctor_controller.dart';

class DoctorFilterBottomSheet extends ConsumerStatefulWidget {
  const DoctorFilterBottomSheet({super.key});

  @override
  ConsumerState<DoctorFilterBottomSheet> createState() => _DoctorFilterBottomSheetState();
}

class _DoctorFilterBottomSheetState extends ConsumerState<DoctorFilterBottomSheet> {
  late String _selectedSpecialty;
  late double _minRating;
  late double _maxFee;
  late bool _availableToday;

  @override
  void initState() {
    super.initState();
    final current = ref.read(doctorFilterProvider);
    _selectedSpecialty = current.selectedSpecialtyId;
    _minRating = current.minRating;
    _maxFee = current.maxFee;
    _availableToday = current.onlyAvailableToday;
  }

  @override
  Widget build(BuildContext context) {
    final specialtiesAsync = ref.watch(specialtiesProvider);

    return Container(
      decoration: const BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.vertical(top: Radius.circular(AppRadius.xxl)),
      ),
      padding: EdgeInsets.only(
        top: AppSpacing.lg,
        left: AppSpacing.lg,
        right: AppSpacing.lg,
        bottom: MediaQuery.of(context).viewInsets.bottom + AppSpacing.xl,
      ),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Drag Handle & Header
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
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                'Filter Doctors',
                style: AppTypography.headlineSm.copyWith(
                  color: AppColors.onSurface,
                  fontWeight: FontWeight.w700,
                ),
              ),
              TextButton(
                onPressed: () {
                  setState(() {
                    _selectedSpecialty = 'all';
                    _minRating = 0.0;
                    _maxFee = 150.0;
                    _availableToday = false;
                  });
                },
                child: Text(
                  'Reset All',
                  style: AppTypography.labelMd.copyWith(color: AppColors.error),
                ),
              ),
            ],
          ),
          const Divider(color: AppColors.outlineVariant),
          AppSpacing.gapVMd,

          // Specialty Filter
          Text(
            'Specialty',
            style: AppTypography.titleMedium.copyWith(
              color: AppColors.onSurface,
              fontWeight: FontWeight.w700,
            ),
          ),
          AppSpacing.gapVSm,
          specialtiesAsync.when(
            data: (specialties) => Wrap(
              spacing: AppSpacing.sm,
              runSpacing: AppSpacing.sm,
              children: specialties.map((s) {
                final isSelected = _selectedSpecialty == s.id;
                return ChoiceChip(
                  label: Text(s.name),
                  selected: isSelected,
                  onSelected: (selected) {
                    setState(() {
                      _selectedSpecialty = selected ? s.id : 'all';
                    });
                  },
                  selectedColor: AppColors.primary,
                  backgroundColor: AppColors.surfaceContainerLow,
                  labelStyle: AppTypography.labelMd.copyWith(
                    color: isSelected ? AppColors.onPrimary : AppColors.onSurface,
                    fontWeight: isSelected ? FontWeight.w700 : FontWeight.w500,
                  ),
                  shape: RoundedRectangleBorder(
                    borderRadius: AppRadius.radiusFull,
                    side: BorderSide(
                      color: isSelected ? AppColors.primary : AppColors.outlineVariant,
                    ),
                  ),
                );
              }).toList(),
            ),
            loading: () => const SizedBox(height: 40),
            error: (_, __) => const SizedBox.shrink(),
          ),
          AppSpacing.gapVLg,

          // Maximum Consultation Fee Slider
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                'Max Consultation Fee',
                style: AppTypography.titleMedium.copyWith(
                  color: AppColors.onSurface,
                  fontWeight: FontWeight.w700,
                ),
              ),
              Text(
                '\$${_maxFee.toInt()}',
                style: AppTypography.titleMedium.copyWith(
                  color: AppColors.primary,
                  fontWeight: FontWeight.w700,
                ),
              ),
            ],
          ),
          Slider(
            value: _maxFee,
            min: 30,
            max: 150,
            divisions: 12,
            activeColor: AppColors.primary,
            inactiveColor: AppColors.outlineVariant,
            label: '\$${_maxFee.toInt()}',
            onChanged: (val) {
              setState(() {
                _maxFee = val;
              });
            },
          ),
          AppSpacing.gapVMd,

          // Minimum Rating Filter
          Text(
            'Minimum Rating',
            style: AppTypography.titleMedium.copyWith(
              color: AppColors.onSurface,
              fontWeight: FontWeight.w700,
            ),
          ),
          AppSpacing.gapVSm,
          Row(
            children: [0.0, 4.0, 4.5, 4.8].map((rating) {
              final isSelected = _minRating == rating;
              return Padding(
                padding: const EdgeInsets.only(right: AppSpacing.sm),
                child: ChoiceChip(
                  label: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      if (rating > 0) ...[
                        const Icon(Icons.star_rounded, size: 16, color: Color(0xFFFFB300)),
                        const SizedBox(width: 4),
                        Text('$rating+'),
                      ] else ...[
                        const Text('Any'),
                      ],
                    ],
                  ),
                  selected: isSelected,
                  onSelected: (selected) {
                    setState(() {
                      _minRating = rating;
                    });
                  },
                  selectedColor: AppColors.primary,
                  backgroundColor: AppColors.surfaceContainerLow,
                  labelStyle: AppTypography.labelMd.copyWith(
                    color: isSelected ? AppColors.onPrimary : AppColors.onSurface,
                  ),
                  shape: RoundedRectangleBorder(
                    borderRadius: AppRadius.radiusFull,
                    side: BorderSide(
                      color: isSelected ? AppColors.primary : AppColors.outlineVariant,
                    ),
                  ),
                ),
              );
            }).toList(),
          ),
          AppSpacing.gapVMd,

          // Availability Toggle
          SwitchListTile(
            contentPadding: EdgeInsets.zero,
            title: Text(
              'Available Today Only',
              style: AppTypography.bodyMd.copyWith(
                color: AppColors.onSurface,
                fontWeight: FontWeight.w600,
              ),
            ),
            subtitle: Text(
              'Show doctors with immediate openings today',
              style: AppTypography.bodySm.copyWith(color: AppColors.onSurfaceVariant),
            ),
            value: _availableToday,
            activeThumbColor: AppColors.primary,
            onChanged: (val) {
              setState(() {
                _availableToday = val;
              });
            },
          ),
          AppSpacing.gapVLg,

          // Action Buttons
          Row(
            children: [
              Expanded(
                child: AppButton(
                  text: 'Apply Filters',
                  onPressed: () {
                    final notifier = ref.read(doctorFilterProvider.notifier);
                    notifier.selectSpecialty(_selectedSpecialty);
                    notifier.setMaxFee(_maxFee);
                    notifier.setMinRating(_minRating);
                    notifier.toggleAvailableToday(_availableToday);
                    Navigator.pop(context);
                  },
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }
}
