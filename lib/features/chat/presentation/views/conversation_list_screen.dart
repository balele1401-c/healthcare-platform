import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';
import '../../../../core/routing/app_routes.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_typography.dart';
import '../../../../shared/widgets/app_avatar.dart';
import '../../../../shared/widgets/app_card.dart';
import '../../../../shared/widgets/app_empty_state.dart';
import '../../../../shared/widgets/app_error.dart';
import '../../../../shared/widgets/app_loading.dart';
import '../controllers/chat_controller.dart';

class ConversationListScreen extends ConsumerWidget {
  const ConversationListScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final conversationsAsync = ref.watch(chatConversationsProvider);

    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        backgroundColor: AppColors.surface,
        elevation: 0,
        leading: Navigator.canPop(context)
            ? IconButton(
                icon: const Icon(Icons.arrow_back_rounded, color: AppColors.onSurface),
                onPressed: () => Navigator.pop(context),
              )
            : null,
        title: Text(
          'Clinical Consultations & Chat',
          style: AppTypography.titleLarge.copyWith(
            color: AppColors.onSurface,
            fontWeight: FontWeight.w700,
          ),
        ),
      ),
      body: conversationsAsync.when(
        data: (conversations) {
          if (conversations.isEmpty) {
            return const AppEmptyState(
              icon: Icons.chat_bubble_outline_rounded,
              title: 'No Conversations',
              message: 'You have no active doctor chat threads.',
            );
          }

          return RefreshIndicator(
            onRefresh: () async => ref.invalidate(chatConversationsProvider),
            child: ListView.separated(
              padding: const EdgeInsets.all(AppSpacing.marginMobile),
              itemCount: conversations.length,
              separatorBuilder: (_, __) => AppSpacing.gapVSm,
              itemBuilder: (context, index) {
                final conv = conversations[index];
                final timeFormatted = DateFormat('h:mm a').format(conv.lastMessageTime);

                return AppCard(
                  onTap: () => context.push(AppRoutes.chatDetail, extra: conv),
                  padding: const EdgeInsets.all(AppSpacing.md),
                  child: Row(
                    children: [
                      Stack(
                        children: [
                          AppAvatar(
                            name: conv.doctorName,
                            imageUrl: conv.doctorAvatarUrl,
                            size: 54,
                          ),
                          if (conv.isDoctorOnline)
                            Positioned(
                              bottom: 0,
                              right: 0,
                              child: Container(
                                width: 14,
                                height: 14,
                                decoration: BoxDecoration(
                                  color: AppColors.success,
                                  shape: BoxShape.circle,
                                  border: Border.all(color: AppColors.surface, width: 2),
                                ),
                              ),
                            ),
                        ],
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
                                    conv.doctorName,
                                    style: AppTypography.titleMedium.copyWith(
                                      color: AppColors.onSurface,
                                      fontWeight: FontWeight.w700,
                                    ),
                                    maxLines: 1,
                                    overflow: TextOverflow.ellipsis,
                                  ),
                                ),
                                Text(
                                  timeFormatted,
                                  style: AppTypography.labelSm.copyWith(
                                    color: conv.unreadCount > 0 ? AppColors.primary : AppColors.onSurfaceVariant,
                                    fontWeight: conv.unreadCount > 0 ? FontWeight.w700 : FontWeight.w400,
                                  ),
                                ),
                              ],
                            ),
                            Text(
                              conv.doctorSpecialty,
                              style: AppTypography.labelSm.copyWith(
                                color: AppColors.primary,
                                fontWeight: FontWeight.w600,
                              ),
                            ),
                            AppSpacing.gapVXs,
                            Row(
                              children: [
                                Expanded(
                                  child: Text(
                                    conv.lastMessage,
                                    style: AppTypography.bodySm.copyWith(
                                      color: conv.unreadCount > 0 ? AppColors.onSurface : AppColors.onSurfaceVariant,
                                      fontWeight: conv.unreadCount > 0 ? FontWeight.w600 : FontWeight.w400,
                                    ),
                                    maxLines: 1,
                                    overflow: TextOverflow.ellipsis,
                                  ),
                                ),
                                if (conv.unreadCount > 0)
                                  Container(
                                    margin: const EdgeInsets.only(left: AppSpacing.sm),
                                    padding: const EdgeInsets.all(6),
                                    decoration: const BoxDecoration(
                                      color: AppColors.primary,
                                      shape: BoxShape.circle,
                                    ),
                                    child: Text(
                                      '${conv.unreadCount}',
                                      style: const TextStyle(
                                        color: AppColors.onPrimary,
                                        fontSize: 10,
                                        fontWeight: FontWeight.bold,
                                      ),
                                    ),
                                  ),
                              ],
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                );
              },
            ),
          );
        },
        loading: () => const Center(child: AppLoading(message: 'Loading conversations...')),
        error: (err, _) => Center(
          child: AppError(
            message: 'Failed to load conversations.',
            onRetry: () => ref.invalidate(chatConversationsProvider),
          ),
        ),
      ),
    );
  }
}
