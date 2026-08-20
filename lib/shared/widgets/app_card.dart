import 'package:flutter/material.dart';
import '../../core/theme/app_colors.dart';
import '../../core/theme/app_radius.dart';
import '../../core/theme/app_shadows.dart';
import '../../core/theme/app_spacing.dart';

class AppCard extends StatefulWidget {
  final Widget child;
  final EdgeInsetsGeometry? padding;
  final VoidCallback? onTap;
  final Color? backgroundColor;
  final BorderRadius? borderRadius;
  final double? width;
  final bool hasBorder;
  final bool hasShadow;
  final Clip clipBehavior;

  const AppCard({
    super.key,
    required this.child,
    this.padding = AppSpacing.paddingCard,
    this.onTap,
    this.backgroundColor,
    this.borderRadius,
    this.width,
    this.hasBorder = true,
    this.hasShadow = true,
    this.clipBehavior = Clip.antiAlias,
  });

  @override
  State<AppCard> createState() => _AppCardState();
}

class _AppCardState extends State<AppCard> {
  bool _isHovering = false;

  @override
  Widget build(BuildContext context) {
    final radius = widget.borderRadius ?? AppRadius.radiusLg;
    final color = widget.backgroundColor ?? AppColors.surfaceContainerLowest;

    List<BoxShadow>? shadows;
    if (widget.hasShadow) {
      shadows = _isHovering && widget.onTap != null ? AppShadows.cardHover : AppShadows.cardAmbient;
    }

    final card = AnimatedScale(
      scale: _isHovering && widget.onTap != null ? 1.015 : 1.0,
      duration: const Duration(milliseconds: 200),
      curve: Curves.easeOutCubic,
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 200),
        curve: Curves.easeOutCubic,
        width: widget.width ?? double.infinity,
        decoration: BoxDecoration(
          color: color,
          borderRadius: radius,
          border: widget.hasBorder
              ? Border.all(color: AppColors.outlineVariant, width: 1)
              : null,
          boxShadow: shadows,
        ),
        child: Material(
          color: Colors.transparent,
          borderRadius: radius,
          clipBehavior: widget.clipBehavior,
          child: widget.onTap != null
              ? InkWell(
                  onTap: widget.onTap,
                  onHover: (hover) => setState(() => _isHovering = hover),
                  borderRadius: radius,
                  child: Padding(
                    padding: widget.padding ?? EdgeInsets.zero,
                    child: widget.child,
                  ),
                )
              : Padding(
                  padding: widget.padding ?? EdgeInsets.zero,
                  child: widget.child,
                ),
        ),
      ),
    );

    if (widget.onTap != null) {
      return MouseRegion(
        onEnter: (_) => setState(() => _isHovering = true),
        onExit: (_) => setState(() => _isHovering = false),
        child: card,
      );
    }

    return card;
  }
}
