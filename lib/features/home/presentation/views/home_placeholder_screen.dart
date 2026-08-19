import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/routing/app_routes.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_radius.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_typography.dart';
import '../../../../shared/widgets/app_avatar.dart';
import '../../../../shared/widgets/app_badge.dart';
import '../../../../shared/widgets/app_button.dart';
import '../../../../shared/widgets/app_card.dart';
import '../../../../shared/widgets/app_dialog.dart';
import '../../../../shared/widgets/app_search_field.dart';
import '../../../../shared/widgets/app_snackbar.dart';
import '../../../auth/presentation/controllers/auth_controller.dart';

class HomePlaceholderScreen extends ConsumerWidget {
  const HomePlaceholderScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final authState = ref.watch(authControllerProvider);
    final user = authState.user;
    final userName = user?.name.isNotEmpty == true ? user!.name : 'Sarah';

    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: Text(
          'HealthCare',
          style: AppTypography.headlineMd.copyWith(
            color: AppColors.primary,
            fontWeight: FontWeight.w700,
          ),
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.notifications_outlined, color: AppColors.onSurfaceVariant),
            onPressed: () {
              AppSnackbar.showInfo(context, 'Notifications will be fully implemented in Phase 2.');
            },
          ),
          Padding(
            padding: const EdgeInsets.only(right: AppSpacing.md),
            child: AppAvatar(
              name: userName,
              size: 36,
            ),
          ),
        ],
      ),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: AppSpacing.paddingScreenAll,
          child: ConstrainedBox(
            constraints: const BoxConstraints(maxWidth: 520),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Greeting
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'Good morning, $userName',
                          style: AppTypography.headlineLgMobile.copyWith(
                            color: AppColors.onSurface,
                            fontWeight: FontWeight.w700,
                          ),
                        ),
                        AppSpacing.gapVXs,
                        Text(
                          'How are you feeling today?',
                          style: AppTypography.bodyMd.copyWith(color: AppColors.onSurfaceVariant),
                        ),
                      ],
                    ),
                  ],
                ),
                AppSpacing.gapVLg,

                // Search Bar
                AppSearchField(
                  onTap: () {
                    AppSnackbar.showInfo(context, 'Doctor Search will be implemented in Phase 2.');
                  },
                ),
                AppSpacing.gapVLg,

                // Phase 1 Foundation Verified Card
                AppCard(
                  backgroundColor: AppColors.primaryFixedDim.withValues(alpha: 0.25),
                  child: Row(
                    children: [
                      Container(
                        width: 48,
                        height: 48,
                        decoration: const BoxDecoration(
                          color: AppColors.primary,
                          shape: BoxShape.circle,
                        ),
                        child: const Icon(
                          Icons.verified_rounded,
                          color: AppColors.onPrimary,
                          size: 28,
                        ),
                      ),
                      AppSpacing.gapHMd,
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const AppBadge(
                              text: 'PHASE 1 FOUNDATION COMPLETE',
                              variant: BadgeVariant.primary,
                            ),
                            AppSpacing.gapVXs,
                            Text(
                              'Authenticated Session Verified',
                              style: AppTypography.bodyMd.copyWith(
                                color: AppColors.onSurface,
                                fontWeight: FontWeight.w600,
                              ),
                            ),
                            Text(
                              'Routing, Design Tokens, and Architecture are operational.',
                              style: AppTypography.bodySm.copyWith(
                                color: AppColors.onSurfaceVariant,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
                AppSpacing.gapVLg,

                // Upcoming Appointment Card Preview
                Text(
                  'Upcoming Appointment',
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
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Row(
                            children: [
                              const AppAvatar(
                                name: 'Dr. Alexander Wright',
                                size: 44,
                              ),
                              AppSpacing.gapHSm,
                              Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    'Dr. Alexander Wright',
                                    style: AppTypography.bodyMd.copyWith(
                                      color: AppColors.onSurface,
                                      fontWeight: FontWeight.w700,
                                    ),
                                  ),
                                  Text(
                                    'Cardiologist • St. Jude Hospital',
                                    style: AppTypography.bodySm.copyWith(color: AppColors.onSurfaceVariant),
                                  ),
                                ],
                              ),
                            ],
                          ),
                          const AppBadge(
                            text: 'Confirmed',
                            variant: BadgeVariant.success,
                          ),
                        ],
                      ),
                      AppSpacing.gapVMd,
                      Container(
                        padding: const EdgeInsets.all(AppSpacing.sm),
                        decoration: BoxDecoration(
                          color: AppColors.surfaceContainerLow,
                          borderRadius: AppRadius.radiusBase,
                        ),
                        child: Row(
                          children: [
                            const Icon(Icons.calendar_today_rounded, size: 16, color: AppColors.primary),
                            AppSpacing.gapHXs,
                            Text(
                              'Tomorrow, Aug 20',
                              style: AppTypography.bodySm.copyWith(
                                color: AppColors.onSurface,
                                fontWeight: FontWeight.w600,
                              ),
                            ),
                            const Spacer(),
                            const Icon(Icons.access_time_rounded, size: 16, color: AppColors.primary),
                            AppSpacing.gapHXs,
                            Text(
                              '10:30 AM',
                              style: AppTypography.bodySm.copyWith(
                                color: AppColors.onSurface,
                                fontWeight: FontWeight.w600,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
                AppSpacing.gapVXl,

                // Sign Out Action (Demonstrating Auth State Clearing)
                AppButton(
                  text: 'Sign Out (Test Session Clearing)',
                  variant: ButtonVariant.outlined,
                  prefixIcon: Icons.logout_rounded,
                  onPressed: () async {
                    final confirm = await AppDialog.showConfirmDialog(
                      context: context,
                      title: 'Sign Out',
                      message: 'Are you sure you want to sign out of the application?',
                      confirmText: 'Sign Out',
                      isDestructive: true,
                    );

                    if (confirm == true) {
                      await ref.read(authControllerProvider.notifier).logout();
                      if (context.mounted) {
                        context.go(AppRoutes.login);
                        AppSnackbar.showInfo(context, 'Signed out successfully.');
                      }
                    }
                  },
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
