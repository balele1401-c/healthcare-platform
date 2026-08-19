import 'package:flutter/material.dart';
import '../../core/theme/app_colors.dart';
import '../../core/theme/app_radius.dart';
import '../../core/theme/app_typography.dart';

enum BadgeVariant { primary, secondary, success, warning, error, neutral }

class AppBadge extends StatelessWidget {
  final String text;
  final BadgeVariant variant;
  final IconData? icon;

  const AppBadge({
    super.key,
    required this.text,
    this.variant = BadgeVariant.primary,
    this.icon,
  });

  @override
  Widget build(BuildContext context) {
    Color backgroundColor;
    Color textColor;

    switch (variant) {
      case BadgeVariant.primary:
        backgroundColor = AppColors.primaryFixedDim.withValues(alpha: 0.3);
        textColor = AppColors.primary;
        break;
      case BadgeVariant.secondary:
        backgroundColor = AppColors.secondaryContainer;
        textColor = AppColors.onSecondaryContainer;
        break;
      case BadgeVariant.success:
        backgroundColor = AppColors.successContainer;
        textColor = AppColors.onSuccessContainer;
        break;
      case BadgeVariant.warning:
        backgroundColor = AppColors.warningContainer;
        textColor = AppColors.onWarningContainer;
        break;
      case BadgeVariant.error:
        backgroundColor = AppColors.errorContainer;
        textColor = AppColors.onErrorContainer;
        break;
      case BadgeVariant.neutral:
        backgroundColor = AppColors.surfaceContainerHigh;
        textColor = AppColors.onSurfaceVariant;
        break;
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
      decoration: BoxDecoration(
        color: backgroundColor,
        borderRadius: AppRadius.radiusFull,
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          if (icon != null) ...[
            Icon(icon, size: 14, color: textColor),
            const SizedBox(width: 4),
          ],
          Text(
            text,
            style: AppTypography.labelMd.copyWith(
              color: textColor,
              fontWeight: FontWeight.w600,
            ),
          ),
        ],
      ),
    );
  }
}
