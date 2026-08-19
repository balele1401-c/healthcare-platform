import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/network/api_client.dart';
import '../../data/repositories/api_chat_repository.dart';
import '../../domain/models/chat_model.dart';
import '../../domain/repositories/chat_repository.dart';

final chatRepositoryProvider = Provider<ChatRepository>((ref) {
  final apiClient = ref.watch(apiClientProvider);
  return ApiChatRepository(apiClient: apiClient);
});

final chatConversationsProvider = FutureProvider<List<ChatConversation>>((ref) async {
  final repository = ref.watch(chatRepositoryProvider);
  return repository.getConversations();
});

final chatConversationDetailProvider = FutureProvider.family<ChatConversation?, String>((ref, id) async {
  final repository = ref.watch(chatRepositoryProvider);
  return repository.getConversationById(id);
});
