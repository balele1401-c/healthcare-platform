import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:healthcare/core/theme/app_theme.dart';
import 'package:healthcare/shared/widgets/app_button.dart';

void main() {
  Widget createWidgetUnderTest(Widget child) {
    return MaterialApp(
      theme: AppTheme.lightTheme,
      home: Scaffold(
        body: Center(child: child),
      ),
    );
  }

  group('AppButton Widget Tests', () {
    testWidgets('renders text properly', (tester) async {
      await tester.pumpWidget(
        createWidgetUnderTest(
          AppButton(
            text: 'Sign In',
            onPressed: () {},
          ),
        ),
      );

      expect(find.text('Sign In'), findsOneWidget);
    });

    testWidgets('triggers callback on tap', (tester) async {
      bool tapped = false;
      await tester.pumpWidget(
        createWidgetUnderTest(
          AppButton(
            text: 'Submit',
            onPressed: () {
              tapped = true;
            },
          ),
        ),
      );

      await tester.tap(find.text('Submit'));
      await tester.pump();

      expect(tapped, isTrue);
    });

    testWidgets('shows loading spinner when isLoading is true', (tester) async {
      await tester.pumpWidget(
        createWidgetUnderTest(
          const AppButton(
            text: 'Processing',
            isLoading: true,
          ),
        ),
      );

      expect(find.byType(CircularProgressIndicator), findsOneWidget);
      expect(find.text('Processing'), findsNothing);
    });
  });
}
