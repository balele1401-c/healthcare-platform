import 'package:flutter/material.dart';

/// Ambient, multi-layered diffuse shadows engineered for enterprise SaaS and clinical clarity.
abstract class AppShadows {
  /// Ultra-subtle ambient shadow for standard flat/card surfaces.
  static const List<BoxShadow> cardAmbient = [
    BoxShadow(
      color: Color.fromRGBO(15, 23, 42, 0.02),
      blurRadius: 8,
      offset: Offset(0, 2),
      spreadRadius: 0,
    ),
    BoxShadow(
      color: Color.fromRGBO(15, 23, 42, 0.04),
      blurRadius: 24,
      offset: Offset(0, 8),
      spreadRadius: -4,
    ),
  ];

  /// Interactive hover state with depth and smooth elevation.
  static const List<BoxShadow> cardHover = [
    BoxShadow(
      color: Color.fromRGBO(15, 23, 42, 0.04),
      blurRadius: 12,
      offset: Offset(0, 4),
      spreadRadius: 0,
    ),
    BoxShadow(
      color: Color.fromRGBO(15, 23, 42, 0.08),
      blurRadius: 32,
      offset: Offset(0, 16),
      spreadRadius: -4,
    ),
  ];

  /// Elevated floating hero element (e.g. Appointment spotlight).
  static const List<BoxShadow> elevated = [
    BoxShadow(
      color: Color.fromRGBO(15, 23, 42, 0.04),
      blurRadius: 10,
      offset: Offset(0, 4),
      spreadRadius: 0,
    ),
    BoxShadow(
      color: Color.fromRGBO(30, 64, 175, 0.08),
      blurRadius: 24,
      offset: Offset(0, 12),
      spreadRadius: -2,
    ),
  ];

  /// Bottom navigation bar top shadow with zero color bleed.
  static const List<BoxShadow> bottomNav = [
    BoxShadow(
      color: Color.fromRGBO(15, 23, 42, 0.03),
      blurRadius: 16,
      offset: Offset(0, -4),
      spreadRadius: 0,
    ),
  ];

  /// Modal / Dropdown / Dialog shadow.
  static const List<BoxShadow> dialogOverlay = [
    BoxShadow(
      color: Color.fromRGBO(15, 23, 42, 0.08),
      blurRadius: 24,
      offset: Offset(0, 8),
      spreadRadius: 0,
    ),
    BoxShadow(
      color: Color.fromRGBO(15, 23, 42, 0.14),
      blurRadius: 48,
      offset: Offset(0, 24),
      spreadRadius: -8,
    ),
  ];
}
