import 'package:flutter/material.dart';
import '../../core/theme/app_colors.dart';
import '../../core/theme/app_radius.dart';
import '../../core/theme/app_spacing.dart';
import '../../core/theme/app_typography.dart';

enum ButtonVariant { primary, secondary, outlined, ghost, error }

class AppButton extends StatelessWidget {
  final String text;
  final VoidCallback? onPressed;
  final ButtonVariant variant;
  final bool isLoading;
  final bool isFullWidth;
  final IconData? prefixIcon;
  final IconData? suffixIcon;
  final double? height;

  final bool isDisabled;

  const AppButton({
    super.key,
    required this.text,
    this.onPressed,
    this.variant = ButtonVariant.primary,
    this.isLoading = false,
    this.isDisabled = false,
    this.isFullWidth = true,
    this.prefixIcon,
    this.suffixIcon,
    this.height = 48.0,
  });

  @override
  Widget build(BuildContext context) {
    final bool effectivelyDisabled = isDisabled || onPressed == null || isLoading;

    Color backgroundColor;
    Color foregroundColor;
    BorderSide borderSide = BorderSide.none;

    switch (variant) {
      case ButtonVariant.primary:
        backgroundColor = effectivelyDisabled ? AppColors.surfaceVariant : AppColors.primary;
        foregroundColor = effectivelyDisabled ? AppColors.outline : AppColors.onPrimary;
        break;
      case ButtonVariant.secondary:
        backgroundColor = effectivelyDisabled ? AppColors.surfaceVariant : AppColors.secondaryContainer;
        foregroundColor = effectivelyDisabled ? AppColors.outline : AppColors.onSecondaryContainer;
        break;
      case ButtonVariant.outlined:
        backgroundColor = AppColors.transparent;
        foregroundColor = effectivelyDisabled ? AppColors.outline : AppColors.primary;
        borderSide = BorderSide(
          color: effectivelyDisabled ? AppColors.outlineVariant : AppColors.primary,
          width: 1.5,
        );
        break;
      case ButtonVariant.ghost:
        backgroundColor = AppColors.transparent;
        foregroundColor = effectivelyDisabled ? AppColors.outline : AppColors.primary;
        break;
      case ButtonVariant.error:
        backgroundColor = effectivelyDisabled ? AppColors.surfaceVariant : AppColors.error;
        foregroundColor = effectivelyDisabled ? AppColors.outline : AppColors.onError;
        break;
    }

    Widget content = Row(
      mainAxisSize: isFullWidth ? MainAxisSize.max : MainAxisSize.min,
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        if (isLoading)
          SizedBox(
            width: 20,
            height: 20,
            child: CircularProgressIndicator(
              strokeWidth: 2,
              valueColor: AlwaysStoppedAnimation<Color>(foregroundColor),
            ),
          )
        else ...[
          if (prefixIcon != null) ...[
            Icon(prefixIcon, size: 20, color: foregroundColor),
            AppSpacing.gapHSm,
          ],
          Flexible(
            child: Text(
              text,
              style: AppTypography.button.copyWith(color: foregroundColor),
              overflow: TextOverflow.ellipsis,
              maxLines: 1,
            ),
          ),
          if (suffixIcon != null) ...[
            AppSpacing.gapHSm,
            Icon(suffixIcon, size: 20, color: foregroundColor),
          ],
        ],
      ],
    );

    return SizedBox(
      width: isFullWidth ? double.infinity : null,
      height: height,
      child: Material(
        color: backgroundColor,
        shape: RoundedRectangleBorder(
          borderRadius: AppRadius.radiusBase,
          side: borderSide,
        ),
        child: InkWell(
          onTap: effectivelyDisabled ? null : onPressed,
          borderRadius: AppRadius.radiusBase,
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: AppSpacing.md),
            child: content,
          ),
        ),
      ),
    );
  }
}
