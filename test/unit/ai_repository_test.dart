import 'package:flutter_test/flutter_test.dart';
import 'package:healthcare/features/ai_assistant/data/repositories/mock_ai_repository.dart';
import 'package:healthcare/features/ai_assistant/domain/models/ai_message_model.dart';

void main() {
  late MockAIRepository repository;

  setUp(() {
    repository = MockAIRepository();
  });

  group('MockAIRepository Tests', () {
    test('getInitialMessages returns welcome message with disclaimer and follow-ups', () async {
      final initial = await repository.getInitialMessages();
      expect(initial.isNotEmpty, true);
      expect(initial.first.role, AIMessageRole.assistant);
      expect(initial.first.suggestedFollowUps, isNotNull);
    });

    test('generateResponse returns clinical preparation response for appointment query', () async {
      final response = await repository.generateResponse('How can I prepare for my doctor appointment?');
      expect(response.role, AIMessageRole.assistant);
      expect(response.text.contains('prepare for your consultation'), true);
      expect(response.suggestedFollowUps!.isNotEmpty, true);
    });

    test('generateResponse returns blood pressure guidance for bp query', () async {
      final response = await repository.generateResponse('What does my blood pressure reading mean?');
      expect(response.text.contains('Blood pressure measures'), true);
    });
  });
}
