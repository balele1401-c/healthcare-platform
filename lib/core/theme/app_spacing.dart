import 'package:flutter/material.dart';

/// Spacing system based on the 8px baseline grid from Clinical Clarity.
abstract class AppSpacing {
  static const double xs = 4.0;
  static const double sm = 8.0;
  static const double md = 16.0;
  static const double lg = 24.0;
  static const double xl = 32.0;
  static const double xxl = 48.0;
  static const double xxxl = 64.0;

  // Screen Margins
  static const double mobileMargin = 16.0;
  static const double marginMobile = 16.0;
  static const double desktopMargin = 32.0;
  static const double gutter = 24.0;

  // EdgeInsets Helpers
  static const EdgeInsets paddingScreen = EdgeInsets.symmetric(horizontal: mobileMargin);
  static const EdgeInsets paddingScreenAll = EdgeInsets.all(mobileMargin);
  static const EdgeInsets paddingCard = EdgeInsets.all(lg);
  static const EdgeInsets paddingCardSm = EdgeInsets.all(md);
  static const EdgeInsets paddingInput = EdgeInsets.symmetric(horizontal: md, vertical: sm + 4);
  static const EdgeInsets paddingButton = EdgeInsets.symmetric(horizontal: lg, vertical: md);
  static const EdgeInsets paddingButtonSm = EdgeInsets.symmetric(horizontal: md, vertical: sm);

  // Vertical SizedBox Spacers
  static const SizedBox gapVXs = SizedBox(height: xs);
  static const SizedBox gapVSm = SizedBox(height: sm);
  static const SizedBox gapVMd = SizedBox(height: md);
  static const SizedBox gapVLg = SizedBox(height: lg);
  static const SizedBox gapVXl = SizedBox(height: xl);
  static const SizedBox gapV2Xl = SizedBox(height: xl);
  static const SizedBox gapVXxl = SizedBox(height: xxl);
  static const SizedBox gapVXxxl = SizedBox(height: xxxl);

  // Horizontal SizedBox Spacers
  static const SizedBox gapHXs = SizedBox(width: xs);
  static const SizedBox gapHSm = SizedBox(width: sm);
  static const SizedBox gapHMd = SizedBox(width: md);
  static const SizedBox gapHLg = SizedBox(width: lg);
  static const SizedBox gapHXl = SizedBox(width: xl);
  static const SizedBox gapH2Xl = SizedBox(width: xl);
  static const SizedBox gapHXxl = SizedBox(width: xxl);
}
