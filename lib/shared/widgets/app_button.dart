import 'package:flutter/material.dart';
import '../../core/theme/app_colors.dart';
import '../../core/theme/app_radius.dart';
import '../../core/theme/app_spacing.dart';
import '../../core/theme/app_typography.dart';

enum ButtonVariant { primary, secondary, outlined, ghost, error }

class AppButton extends StatefulWidget {
  final String text;
  final VoidCallback? onPressed;
  final ButtonVariant variant;
  final bool isLoading;
  final bool isFullWidth;
  final IconData? prefixIcon;
  final IconData? suffixIcon;
  final double height;

  const AppButton({
    super.key,
    required this.text,
    this.onPressed,
    this.variant = ButtonVariant.primary,
    this.isLoading = false,
    this.isFullWidth = true,
    this.prefixIcon,
    this.suffixIcon,
    this.height = 48.0,
  });

  @override
  State<AppButton> createState() => _AppButtonState();
}

class _AppButtonState extends State<AppButton> {
  bool _isHovering = false;

  @override
  Widget build(BuildContext context) {
    final bool disabled = widget.onPressed == null || widget.isLoading;

    Widget buildIcon(IconData icon, Color color) {
      return Icon(icon, size: 18, color: color);
    }

    Widget buildLoading(Color color) {
      return SizedBox(
        width: 18,
        height: 18,
        child: CircularProgressIndicator(
          strokeWidth: 2.2,
          valueColor: AlwaysStoppedAnimation<Color>(color),
        ),
      );
    }

    Widget content(Color foregroundColor) {
      return Row(
        mainAxisSize: widget.isFullWidth ? MainAxisSize.max : MainAxisSize.min,
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          if (widget.isLoading)
            buildLoading(foregroundColor)
          else ...[
            if (widget.prefixIcon != null) ...[
              buildIcon(widget.prefixIcon!, foregroundColor),
              const SizedBox(width: AppSpacing.sm),
            ],
            Flexible(
              child: Text(
                widget.text,
                overflow: TextOverflow.ellipsis,
                maxLines: 1,
                style: AppTypography.button.copyWith(
                  color: foregroundColor,
                  letterSpacing: -0.1,
                  fontWeight: FontWeight.w600,
                ),
              ),
            ),
            if (widget.suffixIcon != null) ...[
              const SizedBox(width: AppSpacing.sm),
              buildIcon(widget.suffixIcon!, foregroundColor),
            ],
          ]
        ],
      );
    }

    Widget buttonWidget;
    switch (widget.variant) {
      case ButtonVariant.primary:
        final fg = disabled ? AppColors.outline : AppColors.onPrimary;
        buttonWidget = Container(
          decoration: BoxDecoration(
            borderRadius: AppRadius.radiusBase,
            gradient: disabled
                ? null
                : const LinearGradient(
                    colors: [Color(0xFF1E40AF), Color(0xFF1E3A8A)],
                    begin: Alignment.topCenter,
                    end: Alignment.bottomCenter,
                  ),
            color: disabled ? AppColors.surfaceContainerHigh : null,
            boxShadow: disabled
                ? null
                : [
                    BoxShadow(
                      color: const Color(0xFF1E40AF).withValues(alpha: _isHovering ? 0.35 : 0.2),
                      offset: const Offset(0, 2),
                      blurRadius: _isHovering ? 8 : 4,
                    )
                  ],
          ),
          child: ElevatedButton(
            onPressed: disabled ? null : widget.onPressed,
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.transparent,
              shadowColor: Colors.transparent,
              disabledBackgroundColor: Colors.transparent,
              disabledForegroundColor: AppColors.outline,
              shape: const RoundedRectangleBorder(
                borderRadius: AppRadius.radiusBase,
              ),
            ),
            child: content(fg),
          ),
        );
        break;
      case ButtonVariant.secondary:
        final fg = disabled ? AppColors.outline : AppColors.onSecondaryContainer;
        buttonWidget = ElevatedButton(
          onPressed: disabled ? null : widget.onPressed,
          style: ElevatedButton.styleFrom(
            backgroundColor: AppColors.secondaryContainer,
            foregroundColor: AppColors.onSecondaryContainer,
            disabledBackgroundColor: AppColors.surfaceContainer,
            disabledForegroundColor: AppColors.outline,
            elevation: 0,
            shape: const RoundedRectangleBorder(
              borderRadius: AppRadius.radiusBase,
              side: BorderSide(color: AppColors.secondaryFixedDim, width: 0.8),
            ),
          ),
          child: content(fg),
        );
        break;
      case ButtonVariant.outlined:
        final fg = disabled ? AppColors.outline : AppColors.onSurface;
        buttonWidget = OutlinedButton(
          onPressed: disabled ? null : widget.onPressed,
          style: OutlinedButton.styleFrom(
            backgroundColor: _isHovering ? AppColors.surfaceContainerLow : AppColors.surface,
            disabledForegroundColor: AppColors.outline,
            shape: const RoundedRectangleBorder(
              borderRadius: AppRadius.radiusBase,
            ),
          ).copyWith(
            side: WidgetStateProperty.resolveWith((states) {
              if (states.contains(WidgetState.disabled)) {
                return const BorderSide(color: AppColors.outlineVariant, width: 1);
              }
              if (states.contains(WidgetState.hovered) || states.contains(WidgetState.focused)) {
                return const BorderSide(color: AppColors.primary, width: 1.2);
              }
              return const BorderSide(color: AppColors.outlineVariant, width: 1);
            }),
          ),
          child: content(fg),
        );
        break;
      case ButtonVariant.ghost:
        final fg = disabled ? AppColors.outline : AppColors.primary;
        buttonWidget = TextButton(
          onPressed: disabled ? null : widget.onPressed,
          style: TextButton.styleFrom(
            disabledForegroundColor: AppColors.outline,
            shape: const RoundedRectangleBorder(
              borderRadius: AppRadius.radiusBase,
            ),
          ),
          child: content(fg),
        );
        break;
      case ButtonVariant.error:
        final fg = disabled ? AppColors.outline : AppColors.onError;
        buttonWidget = ElevatedButton(
          onPressed: disabled ? null : widget.onPressed,
          style: ElevatedButton.styleFrom(
            backgroundColor: AppColors.error,
            foregroundColor: AppColors.onError,
            disabledBackgroundColor: AppColors.surfaceContainer,
            disabledForegroundColor: AppColors.outline,
            elevation: 0,
            shape: const RoundedRectangleBorder(
              borderRadius: AppRadius.radiusBase,
            ),
          ),
          child: content(fg),
        );
        break;
    }

    final sized = SizedBox(
      width: widget.isFullWidth ? double.infinity : null,
      height: widget.height,
      child: buttonWidget,
    );

    if (widget.onPressed != null) {
      return MouseRegion(
        onEnter: (_) => setState(() => _isHovering = true),
        onExit: (_) => setState(() => _isHovering = false),
        child: sized,
      );
    }

    return sized;
  }
}
