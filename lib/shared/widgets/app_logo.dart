import 'package:flutter/material.dart';
import '../../core/theme/app_colors.dart';
import '../../core/theme/app_spacing.dart';
import '../../core/theme/app_typography.dart';

class AppLogo extends StatelessWidget {
  final double iconSize;
  final double? fontSize;
  final bool isLight;

  const AppLogo({
    super.key,
    this.iconSize = 36.0,
    this.fontSize,
    this.isLight = false,
  });

  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisSize: MainAxisSize.min,
      crossAxisAlignment: CrossAxisAlignment.center,
      children: [
        Container(
          width: iconSize,
          height: iconSize,
          decoration: BoxDecoration(
            color: isLight ? AppColors.onPrimary : AppColors.primary,
            borderRadius: BorderRadius.circular(iconSize * 0.25),
          ),
          child: Center(
            child: Icon(
              Icons.health_and_safety_rounded,
              color: isLight ? AppColors.primary : AppColors.onPrimary,
              size: iconSize * 0.65,
            ),
          ),
        ),
        AppSpacing.gapHSm,
        Flexible(
          child: Text(
            'HealthCare',
            style: AppTypography.headlineMd.copyWith(
              color: isLight ? AppColors.onPrimary : AppColors.primary,
              fontWeight: FontWeight.w700,
              fontSize: fontSize ?? 24,
            ),
            overflow: TextOverflow.ellipsis,
          ),
        ),
      ],
    );
  }
}
