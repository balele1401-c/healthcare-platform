import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_radius.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_typography.dart';
import '../../../../shared/widgets/app_empty_state.dart';
import '../../../../shared/widgets/app_error.dart';
import '../../../../shared/widgets/app_skeleton.dart';
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
        scrolledUnderElevation: 0.5,
        leading: Navigator.canPop(context)
            ? IconButton(
                icon: const Icon(Icons.arrow_back_rounded, color: AppColors.onSurface),
                onPressed: () => Navigator.pop(context),
              )
            : null,
        title: Text(
          'Find & Book Doctor',
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
              vertical: AppSpacing.sm + 2,
            ),
            child: Column(
              children: [
                TextField(
                  controller: _searchController,
                  onChanged: (val) {
                    ref.read(doctorFilterProvider.notifier).setSearchQuery(val);
                  },
                  decoration: InputDecoration(
                    hintText: 'Search doctor name, medical specialty, clinic...',
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
                              selectedColor: AppColors.primaryContainer,
                              backgroundColor: AppColors.surfaceContainerLow,
                              labelStyle: AppTypography.labelMd.copyWith(
                                color: isSelected ? AppColors.primary : AppColors.onSurface,
                                fontWeight: isSelected ? FontWeight.w700 : FontWeight.w500,
                              ),
                              shape: RoundedRectangleBorder(
                                borderRadius: AppRadius.radiusFull,
                                side: BorderSide(
                                  color: isSelected ? AppColors.primary : AppColors.outlineVariant,
                                  width: 0.8,
                                ),
                              ),
                            ),
                          );
                        }).toList(),
                      ),
                    );
                  },
                  loading: () => const SizedBox(height: 36),
                  error: (err, stack) => const SizedBox.shrink(),
                ),
              ],
            ),
          ),
          const Divider(height: 1, color: AppColors.outlineVariant),

          // 3. Results Section (Responsive Grid / List)
          Expanded(
            child: LayoutBuilder(
              builder: (context, constraints) {
                final isDesktop = constraints.maxWidth >= 900;
                final isTablet = constraints.maxWidth >= 600 && constraints.maxWidth < 900;

                return doctorsAsync.when(
                  data: (doctors) {
                    if (doctors.isEmpty) {
                      return AppEmptyState(
                        icon: Icons.search_off_rounded,
                        title: 'No Doctors Found',
                        message: 'Try adjusting your search query or resetting active filters.',
                        actionText: 'Reset Filters',
                        onAction: () {
                          _searchController.clear();
                          ref.read(doctorFilterProvider.notifier).resetFilters();
                        },
                      );
                    }

                    if (isDesktop || isTablet) {
                      final crossAxisCount = isDesktop ? 2 : 2;
                      return GridView.builder(
                        padding: const EdgeInsets.all(AppSpacing.lg),
                        gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
                          crossAxisCount: crossAxisCount,
                          crossAxisSpacing: AppSpacing.md,
                          mainAxisSpacing: AppSpacing.md,
                          childAspectRatio: isDesktop ? 2.1 : 1.7,
                        ),
                        itemCount: doctors.length,
                        itemBuilder: (context, index) {
                          return DoctorCard(doctor: doctors[index]);
                        },
                      );
                    }

                    return ListView.separated(
                      padding: const EdgeInsets.all(AppSpacing.marginMobile),
                      itemCount: doctors.length,
                      separatorBuilder: (context, index) => AppSpacing.gapVMd,
                      itemBuilder: (context, index) {
                        return DoctorCard(doctor: doctors[index]);
                      },
                    );
                  },
                  loading: () => ListView.separated(
                    padding: const EdgeInsets.all(AppSpacing.marginMobile),
                    itemCount: 4,
                    separatorBuilder: (context, index) => AppSpacing.gapVMd,
                    itemBuilder: (context, index) => const AppSkeleton(width: double.infinity, height: 160),
                  ),
                  error: (err, _) => Center(
                    child: AppError(
                      message: 'Failed to load doctors list.',
                      onRetry: () => ref.invalidate(doctorListProvider),
                    ),
                  ),
                );
              },
            ),
          ),
        ],
      ),
    );
  }
}
