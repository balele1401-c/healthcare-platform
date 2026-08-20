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
import '../../../../shared/widgets/app_skeleton.dart';
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
        scrolledUnderElevation: 0.5,
        leading: Navigator.canPop(context)
            ? IconButton(
                icon: const Icon(Icons.arrow_back_rounded, color: AppColors.onSurface),
                onPressed: () => Navigator.pop(context),
              )
            : null,
        title: Text(
          'Doctor Messages',
          style: AppTypography.titleLarge.copyWith(
            color: AppColors.onSurface,
            fontWeight: FontWeight.w800,
          ),
        ),
      ),
      body: LayoutBuilder(
        builder: (context, constraints) {
          final isDesktop = constraints.maxWidth >= 900;

          return conversationsAsync.when(
            data: (conversations) {
              if (conversations.isEmpty) {
                return const Center(
                  child: AppEmptyState(
                    icon: Icons.chat_bubble_outline_rounded,
                    title: 'No Conversations',
                    message: 'You have no active doctor chat threads.',
                  ),
                );
              }

              return RefreshIndicator(
                onRefresh: () async => ref.invalidate(chatConversationsProvider),
                child: Center(
                  child: ConstrainedBox(
                    constraints: const BoxConstraints(maxWidth: 860),
                    child: ListView.separated(
                      padding: EdgeInsets.symmetric(
                        horizontal: isDesktop ? AppSpacing.desktopMargin : AppSpacing.marginMobile,
                        vertical: AppSpacing.lg,
                      ),
                      itemCount: conversations.length,
                      separatorBuilder: (context, index) => AppSpacing.gapVSm,
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
                                    size: 52,
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
                                          border: Border.all(color: Colors.white, width: 2),
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
                                              fontSize: 15,
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
                                            padding: const EdgeInsets.symmetric(horizontal: 7, vertical: 2),
                                            decoration: const BoxDecoration(
                                              color: AppColors.primary,
                                              shape: BoxShape.circle,
                                            ),
                                            child: Text(
                                              '${conv.unreadCount}',
                                              style: const TextStyle(
                                                color: Colors.white,
                                                fontSize: 11,
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
                  itemBuilder: (context, index) => const AppSkeleton(width: double.infinity, height: 80),
                ),
              ),
            ),
            error: (err, _) => Center(
              child: AppError(
                message: 'Failed to load conversations.',
                onRetry: () => ref.invalidate(chatConversationsProvider),
              ),
            ),
          );
        },
      ),
    );
  }
}
