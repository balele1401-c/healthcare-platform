import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_radius.dart';
import '../../../../core/theme/app_shadows.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_typography.dart';
import '../../domain/models/ai_message_model.dart';
import '../controllers/ai_assistant_controller.dart';

class AIAssistantScreen extends ConsumerStatefulWidget {
  const AIAssistantScreen({super.key});

  @override
  ConsumerState<AIAssistantScreen> createState() => _AIAssistantScreenState();
}

class _AIAssistantScreenState extends ConsumerState<AIAssistantScreen> {
  final TextEditingController _promptController = TextEditingController();
  final ScrollController _scrollController = ScrollController();

  final List<String> _suggestedPrompts = [
    'How can I prepare for my doctor appointment?',
    'What does my blood pressure reading mean?',
    'How can I improve my sleep quality?',
    'What are healthy foods for heart health?',
  ];

  @override
  void dispose() {
    _promptController.dispose();
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

  void _submitPrompt(String text) {
    if (text.trim().isEmpty) return;
    _promptController.clear();
    ref.read(aiAssistantProvider.notifier).sendMessage(text);
    _scrollToBottom();
  }

  @override
  Widget build(BuildContext context) {
    final aiState = ref.watch(aiAssistantProvider);

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
            Container(
              padding: const EdgeInsets.all(6),
              decoration: BoxDecoration(
                color: AppColors.primaryContainer,
                borderRadius: AppRadius.radiusSm,
                border: Border.all(color: AppColors.primaryFixedDim, width: 0.8),
              ),
              child: const Icon(Icons.auto_awesome_rounded, color: AppColors.primary, size: 18),
            ),
            AppSpacing.gapHSm,
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'AI Clinical Assistant',
                  style: AppTypography.titleMedium.copyWith(
                    color: AppColors.onSurface,
                    fontWeight: FontWeight.w800,
                  ),
                ),
                Text(
                  '24/7 Clinical Triage & Guidance',
                  style: AppTypography.labelSm.copyWith(
                    color: AppColors.onSurfaceVariant,
                    fontSize: 10,
                  ),
                ),
              ],
            ),
          ],
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.restart_alt_rounded, color: AppColors.onSurfaceVariant),
            tooltip: 'Clear Chat',
            onPressed: () {
              ref.read(aiAssistantProvider.notifier).clearConversation();
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
                  // 1. Mandatory Medical Safety Disclaimer Banner
                  Container(
                    width: double.infinity,
                    padding: const EdgeInsets.symmetric(horizontal: AppSpacing.md, vertical: 8),
                    color: AppColors.surfaceContainerLow,
                    child: Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Icon(Icons.shield_outlined, size: 16, color: AppColors.primary),
                        AppSpacing.gapHSm,
                        Expanded(
                          child: Text(
                            'AI Assistant provides general health guidance and does not replace professional medical diagnosis or emergency services.',
                            style: AppTypography.labelSm.copyWith(
                              color: AppColors.onSurfaceVariant,
                              fontSize: 11,
                              height: 1.3,
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),

                  // 2. Messages Stream
                  Expanded(
                    child: ListView.builder(
                      controller: _scrollController,
                      padding: EdgeInsets.symmetric(
                        horizontal: isDesktop ? AppSpacing.desktopMargin : AppSpacing.marginMobile,
                        vertical: AppSpacing.md,
                      ),
                      itemCount: aiState.messages.length,
                      itemBuilder: (context, index) {
                        final msg = aiState.messages[index];
                        final isUser = msg.role == AIMessageRole.user;
                        final timeFormatted = DateFormat('h:mm a').format(msg.timestamp);

                        return Align(
                          alignment: isUser ? Alignment.centerRight : Alignment.centerLeft,
                          child: Container(
                            margin: const EdgeInsets.only(bottom: AppSpacing.md),
                            constraints: BoxConstraints(maxWidth: constraints.maxWidth * 0.75),
                            padding: const EdgeInsets.all(AppSpacing.md),
                            decoration: BoxDecoration(
                              color: isUser ? AppColors.primary : AppColors.surface,
                              borderRadius: BorderRadius.only(
                                topLeft: const Radius.circular(14),
                                topRight: const Radius.circular(14),
                                bottomLeft: Radius.circular(isUser ? 14 : 2),
                                bottomRight: Radius.circular(isUser ? 2 : 14),
                              ),
                              border: isUser ? null : Border.all(color: AppColors.outlineVariant, width: 0.8),
                              boxShadow: AppShadows.cardAmbient,
                            ),
                            child: Column(
                              crossAxisAlignment: isUser ? CrossAxisAlignment.end : CrossAxisAlignment.start,
                              children: [
                                if (!isUser) ...[
                                  Row(
                                    mainAxisSize: MainAxisSize.min,
                                    children: [
                                      const Icon(Icons.auto_awesome_rounded, size: 14, color: AppColors.primary),
                                      const SizedBox(width: 4),
                                      Text(
                                        'HealthCare Clinical AI',
                                        style: AppTypography.labelSm.copyWith(
                                          color: AppColors.primary,
                                          fontWeight: FontWeight.w700,
                                        ),
                                      ),
                                    ],
                                  ),
                                  AppSpacing.gapVXs,
                                ],
                                Text(
                                  msg.text,
                                  style: AppTypography.bodySm.copyWith(
                                    color: isUser ? Colors.white : AppColors.onSurface,
                                    height: 1.5,
                                  ),
                                ),
                                const SizedBox(height: 6),
                                Text(
                                  timeFormatted,
                                  style: AppTypography.labelSm.copyWith(
                                    color: isUser ? Colors.white70 : AppColors.onSurfaceMuted,
                                    fontSize: 10,
                                  ),
                                ),
                              ],
                            ),
                          ),
                        );
                      },
                    ),
                  ),

                  // 3. Suggested Prompt Chips (If fresh chat)
                  if (aiState.messages.length <= 1) ...[
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: AppSpacing.md, vertical: 6),
                      child: SingleChildScrollView(
                        scrollDirection: Axis.horizontal,
                        child: Row(
                          children: _suggestedPrompts.map((prompt) {
                            return Padding(
                              padding: const EdgeInsets.only(right: AppSpacing.sm),
                              child: ActionChip(
                                label: Text(prompt),
                                labelStyle: AppTypography.labelSm.copyWith(
                                  color: AppColors.primary,
                                  fontWeight: FontWeight.w600,
                                ),
                                backgroundColor: AppColors.primaryContainer.withValues(alpha: 0.4),
                                side: const BorderSide(color: AppColors.primaryFixedDim, width: 0.8),
                                shape: RoundedRectangleBorder(borderRadius: AppRadius.radiusFull),
                                onPressed: () => _submitPrompt(prompt),
                              ),
                            );
                          }).toList(),
                        ),
                      ),
                    ),
                  ],

                  // 4. Prompt Input Bar
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
                          Expanded(
                            child: TextField(
                              controller: _promptController,
                              textInputAction: TextInputAction.send,
                              onSubmitted: _submitPrompt,
                              decoration: InputDecoration(
                                hintText: 'Ask a clinical or symptom question...',
                                filled: true,
                                fillColor: AppColors.surfaceContainerLow,
                                contentPadding: const EdgeInsets.symmetric(horizontal: AppSpacing.md, vertical: 12),
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
                              icon: const Icon(Icons.arrow_upward_rounded, color: Colors.white, size: 20),
                              onPressed: () => _submitPrompt(_promptController.text),
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
