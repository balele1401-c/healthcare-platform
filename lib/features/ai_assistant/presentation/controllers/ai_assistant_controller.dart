import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../data/repositories/mock_ai_repository.dart';
import '../../domain/models/ai_message_model.dart';

final aiRepositoryProvider = Provider<MockAIRepository>((ref) {
  return MockAIRepository();
});

class AIState {
  final List<AIMessageModel> messages;
  final bool isThinking;

  const AIState({
    this.messages = const [],
    this.isThinking = false,
  });

  AIState copyWith({
    List<AIMessageModel>? messages,
    bool? isThinking,
  }) {
    return AIState(
      messages: messages ?? this.messages,
      isThinking: isThinking ?? this.isThinking,
    );
  }
}

class AIAssistantNotifier extends StateNotifier<AIState> {
  final MockAIRepository _repository;

  AIAssistantNotifier(this._repository) : super(const AIState()) {
    _initMessages();
  }

  Future<void> _initMessages() async {
    final initial = await _repository.getInitialMessages();
    state = state.copyWith(messages: initial);
  }

  Future<void> sendMessage(String text) async {
    if (text.trim().isEmpty) return;

    final userMessage = AIMessageModel(
      id: 'usr_${DateTime.now().millisecondsSinceEpoch}',
      role: AIMessageRole.user,
      text: text.trim(),
      timestamp: DateTime.now(),
    );

    final updated = List<AIMessageModel>.from(state.messages)..add(userMessage);
    state = state.copyWith(messages: updated, isThinking: true);

    final response = await _repository.generateResponse(text.trim());
    final withResponse = List<AIMessageModel>.from(state.messages)..add(response);
    state = state.copyWith(messages: withResponse, isThinking: false);
  }

  void clearConversation() {
    _initMessages();
  }
}

final aiAssistantProvider = StateNotifierProvider<AIAssistantNotifier, AIState>((ref) {
  final repository = ref.watch(aiRepositoryProvider);
  return AIAssistantNotifier(repository);
});
