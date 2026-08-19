import '../../domain/models/chat_model.dart';
import '../../domain/repositories/chat_repository.dart';

class MockChatRepository implements ChatRepository {
  static final List<ChatConversation> _conversations = [
    ChatConversation(
      id: 'chat_1',
      doctorId: 'doc_1',
      doctorName: 'Dr. Emily Chen',
      doctorSpecialty: 'Senior Cardiologist',
      doctorAvatarUrl: 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=400&q=80',
      isDoctorOnline: true,
      lastMessage: 'Good morning Sarah, please upload your latest home BP log before our 10:30 AM call.',
      lastMessageTime: DateTime.now().subtract(const Duration(minutes: 42)),
      unreadCount: 1,
      messages: [
        ChatMessage(
          id: 'm1',
          senderId: 'doc_1',
          sender: MessageSender.doctor,
          content: 'Hello Sarah! How have you been feeling on the new 5mg Amlodipine dosage?',
          timestamp: DateTime.now().subtract(const Duration(days: 2, hours: 4)),
        ),
        ChatMessage(
          id: 'm2',
          senderId: 'patient_101',
          sender: MessageSender.patient,
          content: 'Hello Dr. Chen! Much better, no ankle swelling and average morning BP has been around 118/76.',
          timestamp: DateTime.now().subtract(const Duration(days: 2, hours: 3)),
        ),
        ChatMessage(
          id: 'm3',
          senderId: 'doc_1',
          sender: MessageSender.doctor,
          content: 'That is great to hear. Excellent progress on the systolic control.',
          timestamp: DateTime.now().subtract(const Duration(days: 2, hours: 2)),
        ),
        ChatMessage(
          id: 'm4',
          senderId: 'doc_1',
          sender: MessageSender.doctor,
          content: 'Good morning Sarah, please upload your latest home BP log before our 10:30 AM call.',
          timestamp: DateTime.now().subtract(const Duration(minutes: 42)),
          isRead: false,
        ),
      ],
    ),
    ChatConversation(
      id: 'chat_2',
      doctorId: 'doc_2',
      doctorName: 'Dr. Marcus Vance',
      doctorSpecialty: 'Consultant Dermatologist',
      doctorAvatarUrl: 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&w=400&q=80',
      isDoctorOnline: false,
      lastMessage: 'Please continue the topical cream for 7 more days.',
      lastMessageTime: DateTime.now().subtract(const Duration(days: 1)),
      unreadCount: 0,
      messages: [
        ChatMessage(
          id: 'mv1',
          senderId: 'patient_101',
          sender: MessageSender.patient,
          content: 'Dr. Vance, the redness has diminished significantly after 3 days.',
          timestamp: DateTime.now().subtract(const Duration(days: 1, hours: 5)),
        ),
        ChatMessage(
          id: 'mv2',
          senderId: 'doc_2',
          sender: MessageSender.doctor,
          content: 'Please continue the topical cream for 7 more days.',
          timestamp: DateTime.now().subtract(const Duration(days: 1)),
        ),
      ],
    ),
  ];

  @override
  Future<List<ChatConversation>> getConversations() async {
    await Future.delayed(const Duration(milliseconds: 200));
    return List.unmodifiable(_conversations);
  }

  @override
  Future<ChatConversation?> getConversationById(String id) async {
    await Future.delayed(const Duration(milliseconds: 150));
    try {
      return _conversations.firstWhere((c) => c.id == id);
    } catch (_) {
      return _conversations.first;
    }
  }

  @override
  Future<ChatMessage> sendMessage(String conversationId, String text, {String? attachmentName}) async {
    await Future.delayed(const Duration(milliseconds: 200));

    final newMessage = ChatMessage(
      id: 'msg_${DateTime.now().millisecondsSinceEpoch}',
      senderId: 'patient_101',
      sender: MessageSender.patient,
      content: text,
      timestamp: DateTime.now(),
      attachmentName: attachmentName,
    );

    final index = _conversations.indexWhere((c) => c.id == conversationId);
    if (index != -1) {
      final conv = _conversations[index];
      final updatedMessages = List<ChatMessage>.from(conv.messages)..add(newMessage);
      _conversations[index] = conv.copyWith(
        messages: updatedMessages,
        lastMessage: text,
        lastMessageTime: DateTime.now(),
      );
    }

    return newMessage;
  }
}
