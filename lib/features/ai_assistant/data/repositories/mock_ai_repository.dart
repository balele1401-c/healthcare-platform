import '../../domain/models/ai_message_model.dart';

class MockAIRepository {
  static final List<AIMessageModel> _initialHistory = [
    AIMessageModel(
      id: 'ai_welcome',
      role: AIMessageRole.assistant,
      text: 'Hello Sarah! I am your AI Health Assistant. I can help explain medical terms, give healthy lifestyle tips, or help you prepare questions for your upcoming doctor consultation.\n\n*Note: I provide general health guidance and cannot provide diagnostic conclusions.*',
      timestamp: DateTime.now().subtract(const Duration(minutes: 5)),
      suggestedFollowUps: [
        'How can I prepare for my doctor appointment?',
        'What does my blood pressure reading mean?',
        'How can I improve my sleep quality?',
        'What are healthy foods for heart health?',
      ],
    ),
  ];

  Future<List<AIMessageModel>> getInitialMessages() async {
    await Future.delayed(const Duration(milliseconds: 150));
    return List.from(_initialHistory);
  }

  Future<AIMessageModel> generateResponse(String userPrompt) async {
    await Future.delayed(const Duration(milliseconds: 700));

    final query = userPrompt.toLowerCase();
    String responseText;
    List<String>? followUps;

    if (query.contains('prepare') || query.contains('appointment')) {
      responseText = 'Here is how you can prepare for your consultation:\n\n'
          '1. **Log Your Symptoms**: Write down when symptoms started, frequency, and severity.\n'
          '2. **Review Medications**: Have your active prescription list (Amlodipine Besylate) ready.\n'
          '3. **Bring Recent Vitals**: Note recent blood pressure and heart rate readings.\n'
          '4. **Key Questions**: Ask about side effects, lifestyle adjustments, and when to schedule your next follow-up.';
      followUps = ['How do I track my BP trends?', 'Can I invite a family member to the video call?'];
    } else if (query.contains('blood pressure') || query.contains('bp')) {
      responseText = 'Blood pressure measures the force of blood against arterial walls:\n\n'
          '• **Systolic (Top number)**: Pressure when your heart beats.\n'
          '• **Diastolic (Bottom number)**: Pressure when your heart rests between beats.\n\n'
          'A reading of **118/76 mmHg** is considered within the **Optimal/Normal range** (<120 systolic and <80 diastolic). Continue monitoring regularly as advised by Dr. Chen.';
      followUps = ['What causes temporary BP spikes?', 'What lifestyle habits help lower BP?'];
    } else if (query.contains('sleep') || query.contains('insomnia')) {
      responseText = 'To improve sleep hygiene and recovery:\n\n'
          '• **Consistent Schedule**: Go to bed and wake up at the same time daily.\n'
          '• **Screen-Free Wind-Down**: Turn off blue light screens 45 minutes before sleep.\n'
          '• **Magnesium & Hydration**: Drink water during the day and avoid heavy meals within 2 hours of bedtime.\n'
          '• **Cool Environment**: Keep your bedroom around 18-20°C (65-68°F).';
      followUps = ['How does caffeine affect REM sleep?', 'Should I take magnesium before bed?'];
    } else {
      responseText = 'Thank you for your question! Maintaining a balanced diet, staying hydrated, getting 150 minutes of moderate cardiovascular exercise per week, and prioritizing restorative sleep are the pillars of long-term wellness.\n\nAlways consult Dr. Emily Chen or your primary physician for specific medical concerns.';
      followUps = ['How can I prepare for my doctor appointment?', 'What does my blood pressure mean?'];
    }

    return AIMessageModel(
      id: 'ai_${DateTime.now().millisecondsSinceEpoch}',
      role: AIMessageRole.assistant,
      text: responseText,
      timestamp: DateTime.now(),
      suggestedFollowUps: followUps,
    );
  }
}
