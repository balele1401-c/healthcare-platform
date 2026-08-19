import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:healthcare/core/theme/app_theme.dart';
import 'package:healthcare/shared/widgets/app_badge.dart';

void main() {
  Widget createWidgetUnderTest(Widget child) {
    return MaterialApp(
      theme: AppTheme.lightTheme,
      home: Scaffold(
        body: Center(child: child),
      ),
    );
  }

  group('AppBadge Widget Tests', () {
    testWidgets('renders badge text and icon', (tester) async {
      await tester.pumpWidget(
        createWidgetUnderTest(
          const AppBadge(
            text: 'Confirmed',
            variant: BadgeVariant.success,
            icon: Icons.check,
          ),
        ),
      );

      expect(find.text('Confirmed'), findsOneWidget);
      expect(find.byIcon(Icons.check), findsOneWidget);
    });
  });
}
