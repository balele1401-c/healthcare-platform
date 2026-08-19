enum AIMessageRole {
  user,
  assistant;
}

class AIMessageModel {
  final String id;
  final AIMessageRole role;
  final String text;
  final DateTime timestamp;
  final List<String>? suggestedFollowUps;

  const AIMessageModel({
    required this.id,
    required this.role,
    required this.text,
    required this.timestamp,
    this.suggestedFollowUps,
  });
}
