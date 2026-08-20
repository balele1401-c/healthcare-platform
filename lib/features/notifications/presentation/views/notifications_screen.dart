import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_radius.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_typography.dart';
import '../../../../shared/widgets/app_card.dart';
import '../../../../shared/widgets/app_empty_state.dart';
import '../../../../shared/widgets/app_error.dart';
import '../../../../shared/widgets/app_skeleton.dart';
import '../../../../shared/widgets/app_snackbar.dart';
import '../../domain/models/notification_model.dart';
import '../controllers/notification_controller.dart';

class NotificationsScreen extends ConsumerWidget {
  const NotificationsScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final selectedCategory = ref.watch(selectedNotificationCategoryProvider);
    final notificationsAsync = ref.watch(notificationsProvider);

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
          'Clinical Alerts & Reminders',
          style: AppTypography.titleLarge.copyWith(
            color: AppColors.onSurface,
            fontWeight: FontWeight.w800,
          ),
        ),
        actions: [
          TextButton(
            onPressed: () async {
              final repo = ref.read(notificationRepositoryProvider);
              await repo.markAllAsRead();
              ref.invalidate(notificationsProvider);
              ref.invalidate(unreadNotificationCountProvider);
              if (context.mounted) {
                AppSnackbar.showSuccess(context, 'All notifications marked as read.');
              }
            },
            child: Text(
              'Mark all read',
              style: AppTypography.labelMd.copyWith(color: AppColors.primary, fontWeight: FontWeight.w700),
            ),
          ),
        ],
      ),
      body: LayoutBuilder(
        builder: (context, constraints) {
          final isDesktop = constraints.maxWidth >= 900;

          return Column(
            children: [
              // 1. Category Filter Chips
              Container(
                color: AppColors.surface,
                padding: EdgeInsets.symmetric(
                  horizontal: isDesktop ? AppSpacing.desktopMargin : AppSpacing.marginMobile,
                  vertical: AppSpacing.sm,
                ),
                child: Center(
                  child: ConstrainedBox(
                    constraints: const BoxConstraints(maxWidth: 860),
                    child: SingleChildScrollView(
                      scrollDirection: Axis.horizontal,
                      child: Row(
                        children: [
                          Padding(
                            padding: const EdgeInsets.only(right: AppSpacing.sm),
                            child: ChoiceChip(
                              label: const Text('All Notifications'),
                              selected: selectedCategory == null,
                              onSelected: (selected) {
                                ref.read(selectedNotificationCategoryProvider.notifier).state = null;
                              },
                              selectedColor: AppColors.primaryContainer,
                              backgroundColor: AppColors.surfaceContainerLow,
                              labelStyle: AppTypography.labelMd.copyWith(
                                color: selectedCategory == null ? AppColors.primary : AppColors.onSurface,
                                fontWeight: selectedCategory == null ? FontWeight.w700 : FontWeight.w500,
                              ),
                              shape: RoundedRectangleBorder(
                                borderRadius: AppRadius.radiusFull,
                                side: BorderSide(
                                  color: selectedCategory == null ? AppColors.primary : AppColors.outlineVariant,
                                  width: 0.8,
                                ),
                              ),
                            ),
                          ),
                          for (final cat in NotificationCategory.values)
                            Padding(
                              padding: const EdgeInsets.only(right: AppSpacing.sm),
                              child: ChoiceChip(
                                label: Text(cat.label),
                                selected: selectedCategory == cat,
                                onSelected: (selected) {
                                  ref.read(selectedNotificationCategoryProvider.notifier).state = selected ? cat : null;
                                },
                                selectedColor: AppColors.primaryContainer,
                                backgroundColor: AppColors.surfaceContainerLow,
                                labelStyle: AppTypography.labelMd.copyWith(
                                  color: selectedCategory == cat ? AppColors.primary : AppColors.onSurface,
                                  fontWeight: selectedCategory == cat ? FontWeight.w700 : FontWeight.w500,
                                ),
                                shape: RoundedRectangleBorder(
                                  borderRadius: AppRadius.radiusFull,
                                  side: BorderSide(
                                    color: selectedCategory == cat ? AppColors.primary : AppColors.outlineVariant,
                                    width: 0.8,
                                  ),
                                ),
                              ),
                            ),
                        ],
                      ),
                    ),
                  ),
                ),
              ),
              const Divider(height: 1, color: AppColors.outlineVariant),

              // 2. Notifications List
              Expanded(
                child: notificationsAsync.when(
                  data: (notifications) {
                    if (notifications.isEmpty) {
                      return const Center(
                        child: AppEmptyState(
                          icon: Icons.notifications_none_rounded,
                          title: 'No Notifications',
                          message: 'You are all caught up with your clinical reminders and alerts.',
                        ),
                      );
                    }

                    return RefreshIndicator(
                      onRefresh: () async {
                        ref.invalidate(notificationsProvider);
                        ref.invalidate(unreadNotificationCountProvider);
                      },
                      child: Center(
                        child: ConstrainedBox(
                          constraints: const BoxConstraints(maxWidth: 860),
                          child: ListView.separated(
                            padding: EdgeInsets.symmetric(
                              horizontal: isDesktop ? AppSpacing.desktopMargin : AppSpacing.marginMobile,
                              vertical: AppSpacing.lg,
                            ),
                            itemCount: notifications.length,
                            separatorBuilder: (context, index) => AppSpacing.gapVSm,
                            itemBuilder: (context, index) {
                              final item = notifications[index];
                              final timeFormatted = DateFormat('MMM d, h:mm a').format(item.timestamp);

                              return AppCard(
                                onTap: () async {
                                  final repo = ref.read(notificationRepositoryProvider);
                                  await repo.markAsRead(item.id);
                                  ref.invalidate(notificationsProvider);
                                  ref.invalidate(unreadNotificationCountProvider);

                                  if (context.mounted && item.routeTarget != null) {
                                    context.push(item.routeTarget!);
                                  }
                                },
                                padding: const EdgeInsets.all(AppSpacing.md),
                                backgroundColor: item.isRead ? AppColors.surface : AppColors.primaryContainer.withValues(alpha: 0.25),
                                child: Row(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Container(
                                      padding: const EdgeInsets.all(10),
                                      decoration: BoxDecoration(
                                        color: item.category.color.withValues(alpha: 0.12),
                                        borderRadius: AppRadius.radiusMd,
                                      ),
                                      child: Icon(item.category.icon, size: 20, color: item.category.color),
                                    ),
                                    AppSpacing.gapHMd,
                                    Expanded(
                                      child: Column(
                                        crossAxisAlignment: CrossAxisAlignment.start,
                                        children: [
                                          Row(
                                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                            children: [
                                              Expanded(
                                                child: Text(
                                                  item.title,
                                                  style: AppTypography.titleMedium.copyWith(
                                                    color: AppColors.onSurface,
                                                    fontWeight: item.isRead ? FontWeight.w600 : FontWeight.w800,
                                                    fontSize: 15,
                                                  ),
                                                ),
                                              ),
                                              if (!item.isRead)
                                                Container(
                                                  width: 8,
                                                  height: 8,
                                                  decoration: const BoxDecoration(
                                                    color: AppColors.primary,
                                                    shape: BoxShape.circle,
                                                  ),
                                                ),
                                            ],
                                          ),
                                          AppSpacing.gapVXs,
                                          Text(
                                            item.description,
                                            style: AppTypography.bodySm.copyWith(
                                              color: AppColors.onSurfaceVariant,
                                              height: 1.4,
                                            ),
                                          ),
                                          AppSpacing.gapVSm,
                                          Text(
                                            timeFormatted,
                                            style: AppTypography.labelSm.copyWith(
                                              color: AppColors.onSurfaceMuted,
                                              fontSize: 11,
                                            ),
                                          ),
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
                        itemCount: 4,
                        separatorBuilder: (context, index) => AppSpacing.gapVSm,
                        itemBuilder: (context, index) => const AppSkeleton(width: double.infinity, height: 90),
                      ),
                    ),
                  ),
                  error: (err, _) => Center(
                    child: AppError(
                      message: 'Failed to load notifications.',
                      onRetry: () => ref.invalidate(notificationsProvider),
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
