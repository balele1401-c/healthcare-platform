import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:healthcare/shared/widgets/app_badge.dart';
import 'package:healthcare/shared/widgets/app_metric_card.dart';

void main() {
  testWidgets('AppMetricCard renders without overflow on narrow dimensions', (tester) async {
    await tester.pumpWidget(
      const MaterialApp(
        home: Scaffold(
          body: Center(
            child: SizedBox(
              width: 160,
              height: 125,
              child: AppMetricCard(
                title: 'Blood Pressure',
                value: '120/80',
                unit: 'mmHg',
                icon: Icons.speed_rounded,
                status: 'Optimal',
                statusVariant: BadgeVariant.primary,
                trend: 'Stable',
              ),
            ),
          ),
        ),
      ),
    );

    expect(find.text('Blood Pressure'), findsOneWidget);
    expect(find.text('120/80'), findsOneWidget);
    expect(find.text('mmHg'), findsOneWidget);
    expect(tester.takeException(), isNull);
  });

  testWidgets('AppMetricCard renders without overflow on wide dimensions', (tester) async {
    await tester.pumpWidget(
      const MaterialApp(
        home: Scaffold(
          body: Center(
            child: SizedBox(
              width: 320,
              height: 180,
              child: AppMetricCard(
                title: 'Heart Rate',
                value: '72',
                unit: 'BPM',
                icon: Icons.favorite_rounded,
                status: 'Resting',
                statusVariant: BadgeVariant.success,
                trend: '+2% vs avg',
              ),
            ),
          ),
        ),
      ),
    );

    expect(find.text('Heart Rate'), findsOneWidget);
    expect(find.text('72'), findsOneWidget);
    expect(tester.takeException(), isNull);
  });
}
