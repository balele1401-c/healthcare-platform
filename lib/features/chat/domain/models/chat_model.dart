enum MessageSender {
  patient,
  doctor;
}

class ChatMessage {
  final String id;
  final String senderId;
  final MessageSender sender;
  final String content;
  final DateTime timestamp;
  final bool isRead;
  final String? attachmentUrl;
  final String? attachmentName;

  const ChatMessage({
    required this.id,
    required this.senderId,
    required this.sender,
    required this.content,
    required this.timestamp,
    this.isRead = true,
    this.attachmentUrl,
    this.attachmentName,
  });

  factory ChatMessage.fromJson(Map<String, dynamic> json) {
    DateTime ts = DateTime.now();
    if (json['created_at'] != null) {
      try {
        ts = DateTime.parse(json['created_at'].toString());
      } catch (_) {}
    }

    final isMine = json['is_mine'] == true;
    final role = json['sender_role']?.toString().toLowerCase();
    final senderType = isMine || role == 'patient' ? MessageSender.patient : MessageSender.doctor;

    return ChatMessage(
      id: json['id']?.toString() ?? '',
      senderId: json['sender_id']?.toString() ?? '',
      sender: senderType,
      content: json['message'] ?? '',
      timestamp: ts,
      isRead: json['read_at'] != null,
      attachmentUrl: json['attachment_path'],
    );
  }
}

class ChatConversation {
  final String id;
  final String doctorId;
  final String doctorName;
  final String doctorSpecialty;
  final String doctorAvatarUrl;
  final bool isDoctorOnline;
  final String lastMessage;
  final DateTime lastMessageTime;
  final int unreadCount;
  final List<ChatMessage> messages;

  const ChatConversation({
    required this.id,
    required this.doctorId,
    required this.doctorName,
    required this.doctorSpecialty,
    required this.doctorAvatarUrl,
    required this.isDoctorOnline,
    required this.lastMessage,
    required this.lastMessageTime,
    required this.unreadCount,
    required this.messages,
  });

  factory ChatConversation.fromJson(Map<String, dynamic> json) {
    DateTime lTime = DateTime.now();
    if (json['last_message_at'] != null) {
      try {
        lTime = DateTime.parse(json['last_message_at'].toString());
      } catch (_) {}
    }

    final rawMessages = json['messages'] as List? ?? [];
    final messageList = rawMessages
        .map((m) => ChatMessage.fromJson(m as Map<String, dynamic>))
        .toList();

    return ChatConversation(
      id: json['id']?.toString() ?? '',
      doctorId: json['doctor_id']?.toString() ?? '',
      doctorName: json['doctor_name'] ?? 'Doctor',
      doctorSpecialty: json['doctor_specialty'] ?? 'Specialist',
      doctorAvatarUrl: json['doctor_photo'] ??
          'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=400&q=80',
      isDoctorOnline: json['status'] == 'active',
      lastMessage: messageList.isNotEmpty ? messageList.last.content : 'Consultation channel active',
      lastMessageTime: lTime,
      unreadCount: 0,
      messages: messageList,
    );
  }

  ChatConversation copyWith({
    String? id,
    String? doctorId,
    String? doctorName,
    String? doctorSpecialty,
    String? doctorAvatarUrl,
    bool? isDoctorOnline,
    String? lastMessage,
    DateTime? lastMessageTime,
    int? unreadCount,
    List<ChatMessage>? messages,
  }) {
    return ChatConversation(
      id: id ?? this.id,
      doctorId: doctorId ?? this.doctorId,
      doctorName: doctorName ?? this.doctorName,
      doctorSpecialty: doctorSpecialty ?? this.doctorSpecialty,
      doctorAvatarUrl: doctorAvatarUrl ?? this.doctorAvatarUrl,
      isDoctorOnline: isDoctorOnline ?? this.isDoctorOnline,
      lastMessage: lastMessage ?? this.lastMessage,
      lastMessageTime: lastMessageTime ?? this.lastMessageTime,
      unreadCount: unreadCount ?? this.unreadCount,
      messages: messages ?? this.messages,
    );
  }
}
