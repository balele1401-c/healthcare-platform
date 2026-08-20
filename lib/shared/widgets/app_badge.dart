import 'package:flutter/material.dart';
import '../../core/theme/app_colors.dart';
import '../../core/theme/app_radius.dart';
import '../../core/theme/app_typography.dart';

enum BadgeVariant { primary, secondary, success, warning, error, neutral, info }

class AppBadge extends StatelessWidget {
  final String text;
  final BadgeVariant variant;
  final IconData? icon;
  final bool hasBorder;

  const AppBadge({
    super.key,
    required this.text,
    this.variant = BadgeVariant.primary,
    this.icon,
    this.hasBorder = true,
  });

  @override
  Widget build(BuildContext context) {
    Color backgroundColor;
    Color textColor;
    Color borderColor;

    switch (variant) {
      case BadgeVariant.primary:
        backgroundColor = AppColors.primaryContainer;
        textColor = AppColors.primary;
        borderColor = AppColors.primaryFixedDim;
        break;
      case BadgeVariant.secondary:
        backgroundColor = AppColors.secondaryContainer;
        textColor = AppColors.onSecondaryContainer;
        borderColor = AppColors.secondaryFixedDim;
        break;
      case BadgeVariant.success:
        backgroundColor = AppColors.successContainer;
        textColor = AppColors.onSuccessContainer;
        borderColor = const Color(0xFFA7F3D0);
        break;
      case BadgeVariant.warning:
        backgroundColor = AppColors.warningContainer;
        textColor = AppColors.onWarningContainer;
        borderColor = const Color(0xFFFDE68A);
        break;
      case BadgeVariant.error:
        backgroundColor = AppColors.errorContainer;
        textColor = AppColors.onErrorContainer;
        borderColor = const Color(0xFFFECDD3);
        break;
      case BadgeVariant.neutral:
        backgroundColor = AppColors.surfaceContainer;
        textColor = AppColors.onSurfaceVariant;
        borderColor = AppColors.outlineVariant;
        break;
      case BadgeVariant.info:
        backgroundColor = AppColors.infoContainer;
        textColor = AppColors.onInfoContainer;
        borderColor = AppColors.tertiaryFixedDim;
        break;
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
      decoration: BoxDecoration(
        color: backgroundColor,
        borderRadius: AppRadius.radiusFull,
        border: hasBorder ? Border.all(color: borderColor, width: 0.8) : null,
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          if (icon != null) ...[
            Icon(icon, size: 13, color: textColor),
            const SizedBox(width: 4),
          ],
          Text(
            text,
            style: AppTypography.labelMd.copyWith(
              color: textColor,
              fontWeight: FontWeight.w600,
              letterSpacing: 0.2,
            ),
          ),
        ],
      ),
    );
  }
}
