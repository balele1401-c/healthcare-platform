import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_radius.dart';
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
          duration: const Duration(milliseconds: 300),
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
        elevation: 1,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_rounded, color: AppColors.onSurface),
          onPressed: () => Navigator.pop(context),
        ),
        title: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(6),
              decoration: BoxDecoration(
                gradient: const LinearGradient(
                  colors: [Color(0xFF8E24AA), AppColors.primary],
                ),
                borderRadius: AppRadius.radiusSm,
              ),
              child: const Icon(Icons.auto_awesome_rounded, color: AppColors.onPrimary, size: 18),
            ),
            AppSpacing.gapHSm,
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'AI Health Assistant',
                  style: AppTypography.titleMedium.copyWith(
                    color: AppColors.onSurface,
                    fontWeight: FontWeight.w700,
                  ),
                ),
                Text(
                  'Clinical Guidance & Preparation',
                  style: AppTypography.labelSm.copyWith(color: AppColors.onSurfaceVariant, fontSize: 10),
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
      body: Column(
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
                    'AI Health Assistant provides general health information and does not replace professional medical diagnosis or treatment.',
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

          // 2. Messages History
          Expanded(
            child: ListView.builder(
              controller: _scrollController,
              padding: const EdgeInsets.all(AppSpacing.marginMobile),
              itemCount: aiState.messages.length,
              itemBuilder: (context, index) {
                final msg = aiState.messages[index];
                final isUser = msg.role == AIMessageRole.user;
                final timeFormatted = DateFormat('h:mm a').format(msg.timestamp);

                return Align(
                  alignment: isUser ? Alignment.centerRight : Alignment.centerLeft,
                  child: Container(
                    margin: const EdgeInsets.only(bottom: AppSpacing.md),
                    constraints: BoxConstraints(maxWidth: MediaQuery.of(context).size.width * 0.82),
                    padding: const EdgeInsets.all(AppSpacing.md),
                    decoration: BoxDecoration(
                      color: isUser ? AppColors.primary : AppColors.surfaceContainerLowest,
                      borderRadius: BorderRadius.only(
                        topLeft: const Radius.circular(AppRadius.lg),
                        topRight: const Radius.circular(AppRadius.lg),
                        bottomLeft: Radius.circular(isUser ? AppRadius.lg : 0),
                        bottomRight: Radius.circular(isUser ? 0 : AppRadius.lg),
                      ),
                      border: isUser ? null : Border.all(color: AppColors.outlineVariant),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withOpacity(0.04),
                          blurRadius: 4,
                          offset: const Offset(0, 2),
                        ),
                      ],
                    ),
                    child: Column(
                      crossAxisAlignment: isUser ? CrossAxisAlignment.end : CrossAxisAlignment.start,
                      children: [
                        if (!isUser) ...[
                          Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              const Icon(Icons.auto_awesome_rounded, size: 16, color: Color(0xFF8E24AA)),
                              AppSpacing.gapHXs,
                              Text(
                                'HealthCare AI',
                                style: AppTypography.labelSm.copyWith(
                                  color: const Color(0xFF8E24AA),
                                  fontWeight: FontWeight.w700,
                                ),
                              ),
                            ],
                          ),
                          AppSpacing.gapVSm,
                        ],
                        Text(
                          msg.text,
                          style: AppTypography.bodyMd.copyWith(
                            color: isUser ? AppColors.onPrimary : AppColors.onSurface,
                            height: 1.45,
                          ),
                        ),
                        AppSpacing.gapVSm,
                        Text(
                          timeFormatted,
                          style: AppTypography.labelSm.copyWith(
                            color: isUser ? AppColors.onPrimary.withOpacity(0.7) : AppColors.outline,
                            fontSize: 10,
                          ),
                        ),
                        if (msg.suggestedFollowUps != null && msg.suggestedFollowUps!.isNotEmpty) ...[
                          AppSpacing.gapVMd,
                          const Divider(color: AppColors.outlineVariant, height: 1),
                          AppSpacing.gapVSm,
                          Text(
                            'Suggested follow-ups:',
                            style: AppTypography.labelSm.copyWith(
                              color: AppColors.primary,
                              fontWeight: FontWeight.w700,
                            ),
                          ),
                          AppSpacing.gapVXs,
                          ...msg.suggestedFollowUps!.map(
                            (prompt) => GestureDetector(
                              onTap: () => _submitPrompt(prompt),
                              child: Padding(
                                padding: const EdgeInsets.only(top: 4),
                                child: Row(
                                  children: [
                                    const Icon(Icons.arrow_forward_rounded, size: 14, color: AppColors.primary),
                                    AppSpacing.gapHXs,
                                    Expanded(
                                      child: Text(
                                        prompt,
                                        style: AppTypography.bodySm.copyWith(
                                          color: AppColors.primary,
                                          fontWeight: FontWeight.w500,
                                        ),
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                            ),
                          ),
                        ],
                      ],
                    ),
                  ),
                );
              },
            ),
          ),

          // 3. AI Thinking Indicator
          if (aiState.isThinking)
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: AppSpacing.marginMobile, vertical: 4),
              child: Row(
                children: [
                  const SizedBox(
                    width: 16,
                    height: 16,
                    child: CircularProgressIndicator(strokeWidth: 2, color: AppColors.primary),
                  ),
                  AppSpacing.gapHMd,
                  Text(
                    'AI Assistant is thinking...',
                    style: AppTypography.labelSm.copyWith(color: AppColors.onSurfaceVariant),
                  ),
                ],
              ),
            ),

          // 4. Quick Suggested Prompts Horizontal Strip
          Container(
            color: AppColors.surface,
            padding: const EdgeInsets.symmetric(horizontal: AppSpacing.marginMobile, vertical: 6),
            child: SingleChildScrollView(
              scrollDirection: Axis.horizontal,
              child: Row(
                children: _suggestedPrompts.map((p) {
                  return Padding(
                    padding: const EdgeInsets.only(right: AppSpacing.sm),
                    child: ActionChip(
                      label: Text(p),
                      labelStyle: AppTypography.labelSm.copyWith(
                        color: AppColors.primary,
                        fontWeight: FontWeight.w600,
                      ),
                      backgroundColor: AppColors.surfaceContainerLow,
                      shape: RoundedRectangleBorder(
                        borderRadius: AppRadius.radiusFull,
                        side: const BorderSide(color: AppColors.outlineVariant),
                      ),
                      onPressed: () => _submitPrompt(p),
                    ),
                  );
                }).toList(),
              ),
            ),
          ),

          // 5. Input Bar
          Container(
            padding: const EdgeInsets.symmetric(
              horizontal: AppSpacing.marginMobile,
              vertical: AppSpacing.sm,
            ),
            decoration: BoxDecoration(
              color: AppColors.surface,
              border: const Border(top: BorderSide(color: AppColors.outlineVariant)),
            ),
            child: SafeArea(
              child: Row(
                children: [
                  Expanded(
                    child: TextField(
                      controller: _promptController,
                      textCapitalization: TextCapitalization.sentences,
                      decoration: InputDecoration(
                        hintText: 'Ask health assistant a question...',
                        filled: true,
                        fillColor: AppColors.surfaceContainerLow,
                        contentPadding: const EdgeInsets.symmetric(horizontal: AppSpacing.md, vertical: 10),
                        border: OutlineInputBorder(
                          borderRadius: AppRadius.radiusFull,
                          borderSide: BorderSide.none,
                        ),
                      ),
                      onSubmitted: (val) => _submitPrompt(val),
                    ),
                  ),
                  AppSpacing.gapHSm,
                  Container(
                    decoration: const BoxDecoration(
                      color: AppColors.primary,
                      shape: BoxShape.circle,
                    ),
                    child: IconButton(
                      icon: const Icon(Icons.send_rounded, color: AppColors.onPrimary, size: 20),
                      onPressed: () => _submitPrompt(_promptController.text),
                    ),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}
