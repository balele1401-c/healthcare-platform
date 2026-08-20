import 'package:flutter/material.dart';

/// Centralized Design Tokens for the Enterprise Healthcare Platform.
/// Built for clinical clarity, high visual hierarchy, and SaaS-grade aesthetics.
abstract class AppColors {
  // Brand Primary (Enterprise Medical Deep Blue / Indigo)
  static const Color primary = Color(0xFF1E40AF); // Blue 800 - Authoritative, Trustworthy
  static const Color primaryLight = Color(0xFF3B82F6); // Blue 500
  static const Color primaryDark = Color(0xFF1E3A8A); // Blue 900
  static const Color primaryContainer = Color(0xFFEFF6FF); // Blue 50
  static const Color onPrimary = Color(0xFFFFFFFF);
  static const Color onPrimaryContainer = Color(0xFF1E3A8A);
  static const Color primaryFixed = Color(0xFFDBEAFE);
  static const Color primaryFixedDim = Color(0xFFBFDBFE);
  static const Color onPrimaryFixed = Color(0xFF172554);
  static const Color onPrimaryFixedVariant = Color(0xFF1E40AF);
  static const Color inversePrimary = Color(0xFF93C5FD);

  // Secondary Accent (Clinical Teal / Cyan - Medical Precision)
  static const Color secondary = Color(0xFF0D9488); // Teal 600
  static const Color secondaryLight = Color(0xFF14B8A6); // Teal 500
  static const Color secondaryDark = Color(0xFF0F766E); // Teal 700
  static const Color secondaryContainer = Color(0xFFF0FDFA); // Teal 50
  static const Color onSecondary = Color(0xFFFFFFFF);
  static const Color onSecondaryContainer = Color(0xFF115E59);
  static const Color secondaryFixed = Color(0xFFCCFBF1);
  static const Color secondaryFixedDim = Color(0xFF99F6E4);
  static const Color onSecondaryFixed = Color(0xFF042F2E);
  static const Color onSecondaryFixedVariant = Color(0xFF0F766E);

  // Tertiary Accent (Sky Blue for Data & Analytics)
  static const Color tertiary = Color(0xFF0284C7); // Sky 600
  static const Color tertiaryContainer = Color(0xFFF0F9FF); // Sky 50
  static const Color onTertiary = Color(0xFFFFFFFF);
  static const Color onTertiaryContainer = Color(0xFF0369A1);
  static const Color tertiaryFixed = Color(0xFFE0F2FE);
  static const Color tertiaryFixedDim = Color(0xFFBAE6FD);
  static const Color onTertiaryFixed = Color(0xFF082F49);
  static const Color onTertiaryFixedVariant = Color(0xFF0369A1);

  // Surfaces & Background Hierarchy (Slate Scale)
  static const Color background = Color(0xFFF8FAFC); // Slate 50 - Ultra clean, calm clinical background
  static const Color onBackground = Color(0xFF0F172A); // Slate 900 - Crisp high contrast
  static const Color surface = Color(0xFFFFFFFF); // Pure white card surfaces
  static const Color surfaceDim = Color(0xFFF1F5F9); // Slate 100 - Subdued surface
  static const Color surfaceBright = Color(0xFFFFFFFF);
  static const Color surfaceVariant = Color(0xFFF1F5F9); // Slate 100
  static const Color onSurface = Color(0xFF0F172A); // Slate 900 - Primary text
  static const Color onSurfaceVariant = Color(0xFF475569); // Slate 600 - Secondary text / labels
  static const Color onSurfaceMuted = Color(0xFF94A3B8); // Slate 400 - Timestamps / helper metadata
  static const Color inverseSurface = Color(0xFF0F172A);
  static const Color inverseOnSurface = Color(0xFFF8FAFC);
  static const Color surfaceTint = Color(0xFF1E40AF);

  // Tonal Container Elevations (Avoid pure flat whitespace)
  static const Color surfaceContainerLowest = Color(0xFFFFFFFF);
  static const Color surfaceContainerLow = Color(0xFFF8FAFC); // Slate 50
  static const Color surfaceContainer = Color(0xFFF1F5F9); // Slate 100
  static const Color surfaceContainerHigh = Color(0xFFE2E8F0); // Slate 200
  static const Color surfaceContainerHighest = Color(0xFFCBD5E1); // Slate 300

  // Hairline Outlines & Dividers
  static const Color outline = Color(0xFF94A3B8); // Slate 400
  static const Color outlineVariant = Color(0xFFE2E8F0); // Slate 200 - Hairline borders
  static const Color outlineSubtle = Color(0xFFF1F5F9); // Slate 100 - Extra soft dividers

  // Semantic Feedback States
  static const Color error = Color(0xFFE11D48); // Rose 600
  static const Color errorContainer = Color(0xFFFFF1F2); // Rose 50
  static const Color onError = Color(0xFFFFFFFF);
  static const Color onErrorContainer = Color(0xFF9F1239);

  static const Color success = Color(0xFF059669); // Emerald 600
  static const Color successContainer = Color(0xFFECFDF5); // Emerald 50
  static const Color onSuccess = Color(0xFFFFFFFF);
  static const Color onSuccessContainer = Color(0xFF065F46);

  static const Color warning = Color(0xFFD97706); // Amber 600
  static const Color warningContainer = Color(0xFFFFFBEB); // Amber 50
  static const Color onWarning = Color(0xFFFFFFFF);
  static const Color onWarningContainer = Color(0xFF92400E);

  static const Color info = Color(0xFF0284C7); // Sky 600
  static const Color infoContainer = Color(0xFFF0F9FF); // Sky 50
  static const Color onInfo = Color(0xFFFFFFFF);
  static const Color onInfoContainer = Color(0xFF075985);

  // Utility Colors
  static const Color transparent = Colors.transparent;
  static const Color shimmerBase = Color(0xFFE2E8F0);
  static const Color shimmerHighlight = Color(0xFFF8FAFC);

  // Status Chip Colors
  static const Color statusActiveBg = Color(0xFFECFDF5);
  static const Color statusActiveText = Color(0xFF047857);
  static const Color statusPendingBg = Color(0xFFFFFBEB);
  static const Color statusPendingText = Color(0xFFB45309);
  static const Color statusUpcomingBg = Color(0xFFEFF6FF);
  static const Color statusUpcomingText = Color(0xFF1D4ED8);
  static const Color statusCancelledBg = Color(0xFFFFF1F2);
  static const Color statusCancelledText = Color(0xFFBE123C);
}
