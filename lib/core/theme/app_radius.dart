import 'package:flutter/material.dart';

/// Border radius tokens based on Clinical Clarity design system.
abstract class AppRadius {
  static const double xs = 2.0;
  static const double sm = 4.0;
  static const double base = 8.0;
  static const double md = 12.0;
  static const double lg = 16.0;
  static const double xl = 24.0;
  static const double xxl = 32.0;
  static const double full = 9999.0;

  // BorderRadius objects
  static const BorderRadius radiusXs = BorderRadius.all(Radius.circular(xs));
  static const BorderRadius radiusSm = BorderRadius.all(Radius.circular(sm));
  static const BorderRadius radiusBase = BorderRadius.all(Radius.circular(base));
  static const BorderRadius radiusMd = BorderRadius.all(Radius.circular(md));
  static const BorderRadius radiusLg = BorderRadius.all(Radius.circular(lg));
  static const BorderRadius radiusXl = BorderRadius.all(Radius.circular(xl));
  static const BorderRadius radiusXxl = BorderRadius.all(Radius.circular(xxl));
  static const BorderRadius radiusFull = BorderRadius.all(Radius.circular(full));

  // Top Rounded (Bottom Sheets & Modals)
  static const BorderRadius radiusTopLg = BorderRadius.vertical(top: Radius.circular(lg));
  static const BorderRadius radiusTopXl = BorderRadius.vertical(top: Radius.circular(xl));
  static const BorderRadius radiusTopXxl = BorderRadius.vertical(top: Radius.circular(xxl));
}
