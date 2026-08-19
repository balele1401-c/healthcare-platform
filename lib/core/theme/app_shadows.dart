import 'package:flutter/material.dart';

/// Ambient and diffused shadows based on Clinical Clarity design system.
abstract class AppShadows {
  /// Base level card shadow: Soft, diffused floating effect
  static const List<BoxShadow> cardAmbient = [
    BoxShadow(
      color: Color.fromRGBO(0, 0, 0, 0.04),
      blurRadius: 15,
      offset: Offset(0, 4),
      spreadRadius: 0,
    ),
  ];

  /// Interactive hover / pressed shadow
  static const List<BoxShadow> cardHover = [
    BoxShadow(
      color: Color.fromRGBO(0, 0, 0, 0.06),
      blurRadius: 20,
      offset: Offset(0, 6),
      spreadRadius: 0,
    ),
  ];

  /// Bottom navigation bar top shadow
  static const List<BoxShadow> bottomNav = [
    BoxShadow(
      color: Color.fromRGBO(0, 0, 0, 0.04),
      blurRadius: 15,
      offset: Offset(0, -4),
      spreadRadius: 0,
    ),
  ];

  /// Modal / Dropdown / Dialog shadow
  static const List<BoxShadow> dialogOverlay = [
    BoxShadow(
      color: Color.fromRGBO(0, 0, 0, 0.10),
      blurRadius: 30,
      offset: Offset(0, 10),
      spreadRadius: 0,
    ),
  ];
}
