import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_radius.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_typography.dart';
import '../../../../shared/widgets/app_empty_state.dart';
import '../../../../shared/widgets/app_error.dart';
import '../../../../shared/widgets/app_loading.dart';
import '../controllers/doctor_controller.dart';
import '../widgets/doctor_card.dart';
import '../widgets/doctor_filter_bottom_sheet.dart';

class DoctorSearchScreen extends ConsumerStatefulWidget {
  const DoctorSearchScreen({super.key});

  @override
  ConsumerState<DoctorSearchScreen> createState() => _DoctorSearchScreenState();
}

class _DoctorSearchScreenState extends ConsumerState<DoctorSearchScreen> {
  final TextEditingController _searchController = TextEditingController();

  @override
  void initState() {
    super.initState();
    _searchController.text = ref.read(doctorFilterProvider).searchQuery;
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  void _openFilterModal() {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (_) => const DoctorFilterBottomSheet(),
    );
  }

  @override
  Widget build(BuildContext context) {
    final filterState = ref.watch(doctorFilterProvider);
    final specialtiesAsync = ref.watch(specialtiesProvider);
    final doctorsAsync = ref.watch(doctorListProvider);

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
          'Find a Doctor',
          style: AppTypography.titleLarge.copyWith(
            color: AppColors.onSurface,
            fontWeight: FontWeight.w700,
          ),
        ),
        actions: [
          IconButton(
            icon: Stack(
              children: [
                const Icon(Icons.tune_rounded, color: AppColors.primary),
                if (filterState.selectedSpecialtyId != 'all' ||
                    filterState.minRating > 0 ||
                    filterState.maxFee < 150 ||
                    filterState.onlyAvailableToday)
                  Positioned(
                    right: 0,
                    top: 0,
                    child: Container(
                      width: 8,
                      height: 8,
                      decoration: const BoxDecoration(
                        color: AppColors.error,
                        shape: BoxShape.circle,
                      ),
                    ),
                  ),
              ],
            ),
            onPressed: _openFilterModal,
          ),
        ],
      ),
      body: Column(
        children: [
          // 1. Search Bar & Filter Header
          Container(
            color: AppColors.surface,
            padding: const EdgeInsets.symmetric(
              horizontal: AppSpacing.marginMobile,
              vertical: AppSpacing.sm,
            ),
            child: Column(
              children: [
                TextField(
                  controller: _searchController,
                  onChanged: (val) {
                    ref.read(doctorFilterProvider.notifier).setSearchQuery(val);
                  },
                  decoration: InputDecoration(
                    hintText: 'Search doctor, specialty, clinic...',
                    prefixIcon: const Icon(Icons.search_rounded, color: AppColors.onSurfaceVariant),
                    suffixIcon: _searchController.text.isNotEmpty
                        ? IconButton(
                            icon: const Icon(Icons.clear_rounded, size: 20),
                            onPressed: () {
                              _searchController.clear();
                              ref.read(doctorFilterProvider.notifier).setSearchQuery('');
                            },
                          )
                        : null,
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
                AppSpacing.gapVSm,

                // 2. Specialty Quick Chips Carousel
                specialtiesAsync.when(
                  data: (specialties) {
                    return SingleChildScrollView(
                      scrollDirection: Axis.horizontal,
                      child: Row(
                        children: specialties.map((s) {
                          final isSelected = filterState.selectedSpecialtyId == s.id;
                          return Padding(
                            padding: const EdgeInsets.only(right: AppSpacing.sm),
                            child: ChoiceChip(
                              label: Text(s.name),
                              selected: isSelected,
                              onSelected: (selected) {
                                ref.read(doctorFilterProvider.notifier).selectSpecialty(
                                      selected ? s.id : 'all',
                                    );
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
                            ),
                          );
                        }).toList(),
                      ),
                    );
                  },
                  loading: () => const SizedBox(height: 36),
                  error: (_, __) => const SizedBox.shrink(),
                ),
              ],
            ),
          ),
          const Divider(height: 1, color: AppColors.outlineVariant),

          // 3. Results Section
          Expanded(
            child: doctorsAsync.when(
              data: (doctors) {
                if (doctors.isEmpty) {
                  return AppEmptyState(
                    icon: Icons.search_off_rounded,
                    title: 'No Doctors Found',
                    message: 'Try adjusting your search keywords or resetting your active filters.',
                    actionText: 'Reset Filters',
                    onAction: () {
                      _searchController.clear();
                      ref.read(doctorFilterProvider.notifier).resetFilters();
                    },
                  );
                }

                return ListView.separated(
                  padding: const EdgeInsets.all(AppSpacing.marginMobile),
                  itemCount: doctors.length,
                  separatorBuilder: (_, __) => AppSpacing.gapVMd,
                  itemBuilder: (context, index) {
                    return DoctorCard(doctor: doctors[index]);
                  },
                );
              },
              loading: () => const Center(child: AppLoading(message: 'Finding doctors...')),
              error: (err, _) => Center(
                child: AppError(
                  message: 'Failed to load doctors list.',
                  onRetry: () => ref.invalidate(doctorListProvider),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
