import 'package:flutter/material.dart';
import '../../core/theme/app_colors.dart';
import '../../core/theme/app_radius.dart';
import '../../core/theme/app_shadows.dart';
import '../../core/theme/app_typography.dart';
import 'app_badge.dart';

/// Clean high-impact metric component for clinical and vital sign statistics.
class AppMetricCard extends StatefulWidget {
  final String title;
  final String value;
  final String unit;
  final IconData icon;
  final Color? accentColor;
  final String? status;
  final BadgeVariant statusVariant;
  final String? trend;
  final bool isTrendPositive;
  final VoidCallback? onTap;

  const AppMetricCard({
    super.key,
    required this.title,
    required this.value,
    required this.unit,
    required this.icon,
    this.accentColor,
    this.status,
    this.statusVariant = BadgeVariant.success,
    this.trend,
    this.isTrendPositive = true,
    this.onTap,
  });

  @override
  State<AppMetricCard> createState() => _AppMetricCardState();
}

class _AppMetricCardState extends State<AppMetricCard> {
  bool _isHovering = false;

  @override
  Widget build(BuildContext context) {
    final accent = widget.accentColor ?? AppColors.primary;

    return MouseRegion(
      onEnter: (_) => setState(() => _isHovering = true),
      onExit: (_) => setState(() => _isHovering = false),
      child: AnimatedScale(
        scale: _isHovering && widget.onTap != null ? 1.015 : 1.0,
        duration: const Duration(milliseconds: 180),
        curve: Curves.easeOutCubic,
        child: AnimatedContainer(
          duration: const Duration(milliseconds: 180),
          curve: Curves.easeOutCubic,
          padding: const EdgeInsets.symmetric(
            horizontal: 14,
            vertical: 10,
          ),
          decoration: BoxDecoration(
            color: AppColors.surface,
            borderRadius: AppRadius.radiusLg,
            border: Border.all(
              color: _isHovering ? accent.withValues(alpha: 0.4) : AppColors.outlineVariant,
              width: 0.8,
            ),
            boxShadow: _isHovering && widget.onTap != null ? AppShadows.cardHover : AppShadows.cardAmbient,
          ),
          child: InkWell(
            onTap: widget.onTap,
            borderRadius: AppRadius.radiusLg,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                // Top Row: Icon + Status Badge
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Container(
                      padding: const EdgeInsets.all(6),
                      decoration: BoxDecoration(
                        color: accent.withValues(alpha: 0.08),
                        borderRadius: AppRadius.radiusMd,
                      ),
                      child: Icon(widget.icon, size: 18, color: accent),
                    ),
                    if (widget.status != null) ...[
                      const SizedBox(width: 4),
                      Flexible(
                        child: FittedBox(
                          fit: BoxFit.scaleDown,
                          alignment: Alignment.centerRight,
                          child: AppBadge(
                            text: widget.status!,
                            variant: widget.statusVariant,
                          ),
                        ),
                      ),
                    ],
                  ],
                ),

                const SizedBox(height: 4),

                // Middle: Large Hero Value + Unit (Fitted to avoid horizontal overflow)
                Row(
                  crossAxisAlignment: CrossAxisAlignment.baseline,
                  textBaseline: TextBaseline.alphabetic,
                  children: [
                    Flexible(
                      child: FittedBox(
                        fit: BoxFit.scaleDown,
                        alignment: Alignment.centerLeft,
                        child: Text(
                          widget.value,
                          style: AppTypography.headlineLg.copyWith(
                            color: AppColors.onSurface,
                            fontWeight: FontWeight.w800,
                            letterSpacing: -0.5,
                          ),
                        ),
                      ),
                    ),
                    if (widget.unit.isNotEmpty) ...[
                      const SizedBox(width: 4),
                      Text(
                        widget.unit,
                        style: AppTypography.labelMd.copyWith(
                          color: AppColors.onSurfaceVariant,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                    ],
                  ],
                ),

                const SizedBox(height: 2),

                // Bottom: Title & Trend
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Expanded(
                      child: Text(
                        widget.title,
                        style: AppTypography.bodySm.copyWith(
                          color: AppColors.onSurfaceVariant,
                          fontWeight: FontWeight.w500,
                        ),
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ),
                    if (widget.trend != null) ...[
                      const SizedBox(width: 4),
                      Flexible(
                        child: FittedBox(
                          fit: BoxFit.scaleDown,
                          alignment: Alignment.centerRight,
                          child: Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              Icon(
                                widget.isTrendPositive ? Icons.trending_up_rounded : Icons.trending_down_rounded,
                                size: 14,
                                color: widget.isTrendPositive ? AppColors.success : AppColors.error,
                              ),
                              const SizedBox(width: 2),
                              Text(
                                widget.trend!,
                                style: AppTypography.labelSm.copyWith(
                                  color: widget.isTrendPositive ? AppColors.success : AppColors.error,
                                  fontWeight: FontWeight.w600,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                    ],
                  ],
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
