import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:healthcare/core/theme/app_theme.dart';
import 'package:healthcare/shared/widgets/app_text_field.dart';

void main() {
  Widget createWidgetUnderTest(Widget child) {
    return MaterialApp(
      theme: AppTheme.lightTheme,
      home: Scaffold(
        body: Center(child: child),
      ),
    );
  }

  group('AppTextField Widget Tests', () {
    testWidgets('renders label and hint text', (tester) async {
      await tester.pumpWidget(
        createWidgetUnderTest(
          const AppTextField(
            label: 'Email Address',
            hintText: 'name@example.com',
          ),
        ),
      );

      expect(find.text('Email Address'), findsOneWidget);
      expect(find.text('name@example.com'), findsOneWidget);
    });

    testWidgets('allows entering text', (tester) async {
      final controller = TextEditingController();

      await tester.pumpWidget(
        createWidgetUnderTest(
          AppTextField(
            controller: controller,
            hintText: 'Enter value',
          ),
        ),
      );

      await tester.enterText(find.byType(TextFormField), 'Hello Flutter');
      expect(controller.text, 'Hello Flutter');
    });
  });
}
