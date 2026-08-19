import 'package:flutter/material.dart';

/// Design tokens for the "Clinical Clarity" design system.
/// Extracted and verified from Google Stitch design export.
abstract class AppColors {
  // Brand Primary Palette
  static const Color primary = Color(0xFF0050CB);
  static const Color primaryContainer = Color(0xFF0066FF);
  static const Color onPrimary = Color(0xFFFFFFFF);
  static const Color onPrimaryContainer = Color(0xFFF8F7FF);
  static const Color primaryFixed = Color(0xFFDAE1FF);
  static const Color primaryFixedDim = Color(0xFFB3C5FF);
  static const Color onPrimaryFixed = Color(0xFF001849);
  static const Color onPrimaryFixedVariant = Color(0xFF003FA4);
  static const Color inversePrimary = Color(0xFFB3C5FF);

  // Secondary Palette (Medical Teal)
  static const Color secondary = Color(0xFF006A6A);
  static const Color secondaryContainer = Color(0xFF90EFEF);
  static const Color onSecondary = Color(0xFFFFFFFF);
  static const Color onSecondaryContainer = Color(0xFF006E6E);
  static const Color secondaryFixed = Color(0xFF93F2F2);
  static const Color secondaryFixedDim = Color(0xFF76D6D5);
  static const Color onSecondaryFixed = Color(0xFF002020);
  static const Color onSecondaryFixedVariant = Color(0xFF004F4F);

  // Tertiary Neutral Accent Palette
  static const Color tertiary = Color(0xFF555A5D);
  static const Color tertiaryContainer = Color(0xFF6D7276);
  static const Color onTertiary = Color(0xFFFFFFFF);
  static const Color onTertiaryContainer = Color(0xFFF4F8FC);
  static const Color tertiaryFixed = Color(0xFFDFE3E7);
  static const Color tertiaryFixedDim = Color(0xFFC3C7CB);
  static const Color onTertiaryFixed = Color(0xFF171C1F);
  static const Color onTertiaryFixedVariant = Color(0xFF43474B);

  // Surface & Background Hierarchy
  static const Color background = Color(0xFFF9F9FF);
  static const Color onBackground = Color(0xFF161C27);
  static const Color surface = Color(0xFFF9F9FF);
  static const Color surfaceDim = Color(0xFFD4DAEA);
  static const Color surfaceBright = Color(0xFFF9F9FF);
  static const Color surfaceVariant = Color(0xFFDDE2F3);
  static const Color onSurface = Color(0xFF161C27);
  static const Color onSurfaceVariant = Color(0xFF424656);
  static const Color inverseSurface = Color(0xFF2A303D);
  static const Color inverseOnSurface = Color(0xFFECF0FF);
  static const Color surfaceTint = Color(0xFF0054D6);

  // Container Elevations
  static const Color surfaceContainerLowest = Color(0xFFFFFFFF); // Card surfaces
  static const Color surfaceContainerLow = Color(0xFFF1F3FF);
  static const Color surfaceContainer = Color(0xFFE8EEFF);
  static const Color surfaceContainerHigh = Color(0xFFE3E8F9);
  static const Color surfaceContainerHighest = Color(0xFFDDE2F3);

  // Outlines & Borders
  static const Color outline = Color(0xFF727687);
  static const Color outlineVariant = Color(0xFFC2C6D8);

  // Semantic States
  static const Color error = Color(0xFFBA1A1A);
  static const Color errorContainer = Color(0xFFFFDAD6);
  static const Color onError = Color(0xFFFFFFFF);
  static const Color onErrorContainer = Color(0xFF93000A);

  static const Color success = Color(0xFF107C41);
  static const Color successContainer = Color(0xFFD1F2D9);
  static const Color onSuccess = Color(0xFFFFFFFF);
  static const Color onSuccessContainer = Color(0xFF0B582E);

  static const Color warning = Color(0xFFB57500);
  static const Color warningContainer = Color(0xFFFFF1CC);
  static const Color onWarning = Color(0xFFFFFFFF);
  static const Color onWarningContainer = Color(0xFF7A4E00);

  static const Color info = Color(0xFF0050CB);
  static const Color infoContainer = Color(0xFFDAE1FF);

  // Transparent / Shimmer
  static const Color transparent = Colors.transparent;
  static const Color shimmerBase = Color(0xFFE8EEFF);
  static const Color shimmerHighlight = Color(0xFFF9F9FF);
}
