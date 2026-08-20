import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_radius.dart';
import '../../../../core/theme/app_shadows.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_typography.dart';
import '../../../../shared/widgets/app_avatar.dart';
import '../../../../shared/widgets/app_snackbar.dart';
import '../../domain/models/chat_model.dart';
import '../controllers/chat_controller.dart';

class DoctorChatScreen extends ConsumerStatefulWidget {
  final ChatConversation conversation;

  const DoctorChatScreen({
    super.key,
    required this.conversation,
  });

  @override
  ConsumerState<DoctorChatScreen> createState() => _DoctorChatScreenState();
}

class _DoctorChatScreenState extends ConsumerState<DoctorChatScreen> {
  late ChatConversation _conversation;
  final TextEditingController _messageController = TextEditingController();
  final ScrollController _scrollController = ScrollController();

  @override
  void initState() {
    super.initState();
    _conversation = widget.conversation;
  }

  @override
  void dispose() {
    _messageController.dispose();
    _scrollController.dispose();
    super.dispose();
  }

  void _scrollToBottom() {
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (_scrollController.hasClients) {
        _scrollController.animateTo(
          _scrollController.position.maxScrollExtent,
          duration: const Duration(milliseconds: 250),
          curve: Curves.easeOut,
        );
      }
    });
  }

  Future<void> _handleSendMessage() async {
    final text = _messageController.text.trim();
    if (text.isEmpty) return;

    _messageController.clear();
    final repo = ref.read(chatRepositoryProvider);
    final newMsg = await repo.sendMessage(_conversation.id, text);

    setState(() {
      _conversation = _conversation.copyWith(
        messages: List<ChatMessage>.from(_conversation.messages)..add(newMsg),
        lastMessage: text,
        lastMessageTime: DateTime.now(),
      );
    });
    ref.invalidate(chatConversationsProvider);
    _scrollToBottom();
  }

  @override
  Widget build(BuildContext context) {
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
        title: Row(
          children: [
            AppAvatar(
              name: _conversation.doctorName,
              imageUrl: _conversation.doctorAvatarUrl,
              size: 38,
            ),
            AppSpacing.gapHSm,
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    _conversation.doctorName,
                    style: AppTypography.titleMedium.copyWith(
                      color: AppColors.onSurface,
                      fontWeight: FontWeight.w700,
                    ),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                  Text(
                    _conversation.isDoctorOnline ? 'Online • ${_conversation.doctorSpecialty}' : _conversation.doctorSpecialty,
                    style: AppTypography.labelSm.copyWith(
                      color: _conversation.isDoctorOnline ? AppColors.success : AppColors.onSurfaceVariant,
                      fontSize: 11,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.videocam_outlined, color: AppColors.primary),
            onPressed: () {
              AppSnackbar.showInfo(context, 'Starting telemedicine video session with ${_conversation.doctorName}...');
            },
          ),
        ],
      ),
      body: LayoutBuilder(
        builder: (context, constraints) {
          final isDesktop = constraints.maxWidth >= 900;

          return Center(
            child: ConstrainedBox(
              constraints: const BoxConstraints(maxWidth: 860),
              child: Column(
                children: [
                  // 1. Messages Stream
                  Expanded(
                    child: ListView.builder(
                      controller: _scrollController,
                      padding: EdgeInsets.symmetric(
                        horizontal: isDesktop ? AppSpacing.desktopMargin : AppSpacing.marginMobile,
                        vertical: AppSpacing.md,
                      ),
                      itemCount: _conversation.messages.length,
                      itemBuilder: (context, index) {
                        final msg = _conversation.messages[index];
                        final isPatient = msg.sender == MessageSender.patient;
                        final timeFormatted = DateFormat('h:mm a').format(msg.timestamp);

                        return Align(
                          alignment: isPatient ? Alignment.centerRight : Alignment.centerLeft,
                          child: Container(
                            margin: const EdgeInsets.only(bottom: AppSpacing.sm + 2),
                            constraints: BoxConstraints(maxWidth: constraints.maxWidth * 0.75),
                            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                            decoration: BoxDecoration(
                              color: isPatient ? AppColors.primary : AppColors.surface,
                              borderRadius: BorderRadius.only(
                                topLeft: const Radius.circular(14),
                                topRight: const Radius.circular(14),
                                bottomLeft: Radius.circular(isPatient ? 14 : 2),
                                bottomRight: Radius.circular(isPatient ? 2 : 14),
                              ),
                              border: isPatient ? null : Border.all(color: AppColors.outlineVariant, width: 0.8),
                              boxShadow: AppShadows.cardAmbient,
                            ),
                            child: Column(
                              crossAxisAlignment: isPatient ? CrossAxisAlignment.end : CrossAxisAlignment.start,
                              children: [
                                Text(
                                  msg.content,
                                  style: AppTypography.bodySm.copyWith(
                                    color: isPatient ? Colors.white : AppColors.onSurface,
                                    height: 1.4,
                                  ),
                                ),
                                const SizedBox(height: 4),
                                Row(
                                  mainAxisSize: MainAxisSize.min,
                                  children: [
                                    Text(
                                      timeFormatted,
                                      style: AppTypography.labelSm.copyWith(
                                        color: isPatient ? Colors.white.withValues(alpha: 0.7) : AppColors.onSurfaceMuted,
                                        fontSize: 10,
                                      ),
                                    ),
                                    if (isPatient) ...[
                                      const SizedBox(width: 4),
                                      const Icon(Icons.done_all_rounded, size: 13, color: Colors.white70),
                                    ],
                                  ],
                                ),
                              ],
                            ),
                          ),
                        );
                      },
                    ),
                  ),

                  // 2. Chat Input Bar
                  Container(
                    padding: const EdgeInsets.all(AppSpacing.md),
                    decoration: const BoxDecoration(
                      color: AppColors.surface,
                      border: Border(
                        top: BorderSide(color: AppColors.outlineVariant, width: 0.8),
                      ),
                    ),
                    child: SafeArea(
                      child: Row(
                        children: [
                          IconButton(
                            icon: const Icon(Icons.attach_file_rounded, color: AppColors.onSurfaceVariant),
                            onPressed: () {
                              AppSnackbar.showInfo(context, 'Medical record attachment dialog opened.');
                            },
                          ),
                          Expanded(
                            child: TextField(
                              controller: _messageController,
                              textInputAction: TextInputAction.send,
                              onSubmitted: (_) => _handleSendMessage(),
                              decoration: InputDecoration(
                                hintText: 'Type a message to Dr. ${_conversation.doctorName.split(' ').last}...',
                                filled: true,
                                fillColor: AppColors.surfaceContainerLow,
                                contentPadding: const EdgeInsets.symmetric(horizontal: AppSpacing.md, vertical: 10),
                                border: OutlineInputBorder(
                                  borderRadius: AppRadius.radiusFull,
                                  borderSide: const BorderSide(color: AppColors.outlineVariant, width: 0.8),
                                ),
                                enabledBorder: OutlineInputBorder(
                                  borderRadius: AppRadius.radiusFull,
                                  borderSide: const BorderSide(color: AppColors.outlineVariant, width: 0.8),
                                ),
                                focusedBorder: OutlineInputBorder(
                                  borderRadius: AppRadius.radiusFull,
                                  borderSide: const BorderSide(color: AppColors.primary, width: 1.5),
                                ),
                              ),
                            ),
                          ),
                          AppSpacing.gapHSm,
                          Container(
                            decoration: const BoxDecoration(
                              color: AppColors.primary,
                              shape: BoxShape.circle,
                            ),
                            child: IconButton(
                              icon: const Icon(Icons.send_rounded, color: Colors.white, size: 18),
                              onPressed: _handleSendMessage,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ],
              ),
            ),
          );
        },
      ),
    );
  }
}
