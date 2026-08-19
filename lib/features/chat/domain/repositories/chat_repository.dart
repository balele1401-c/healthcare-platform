import '../models/chat_model.dart';

abstract class ChatRepository {
  Future<List<ChatConversation>> getConversations();

  Future<ChatConversation?> getConversationById(String id);

  Future<ChatMessage> sendMessage(
    String conversationId,
    String text, {
    String? attachmentName,
  });
}
