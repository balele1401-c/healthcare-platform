import '../../../../core/network/api_client.dart';
import '../../domain/models/chat_model.dart';
import '../../domain/repositories/chat_repository.dart';

class ApiChatRepository implements ChatRepository {
  final ApiClient _apiClient;

  ApiChatRepository({required ApiClient apiClient}) : _apiClient = apiClient;

  @override
  Future<List<ChatConversation>> getConversations() async {
    final response = await _apiClient.get('/conversations');
    final data = response.data['data'] as List? ?? [];

    return data
        .map((item) => ChatConversation.fromJson(item as Map<String, dynamic>))
        .toList();
  }

  @override
  Future<ChatConversation?> getConversationById(String id) async {
    try {
      final response = await _apiClient.get('/conversations/$id');
      final data = response.data['data'] as Map<String, dynamic>?;
      if (data != null) {
        return ChatConversation.fromJson(data);
      }
      return null;
    } catch (_) {
      return null;
    }
  }

  @override
  Future<ChatMessage> sendMessage(
    String conversationId,
    String text, {
    String? attachmentName,
  }) async {
    final response = await _apiClient.post(
      '/conversations/$conversationId/messages',
      data: {'message': text},
    );

    final data = response.data['data'] as Map<String, dynamic>;
    return ChatMessage.fromJson(data);
  }
}
