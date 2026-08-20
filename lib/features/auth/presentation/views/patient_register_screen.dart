import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/routing/app_routes.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_radius.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_typography.dart';
import '../../../../core/utils/validators.dart';
import '../../../../shared/widgets/app_badge.dart';
import '../../../../shared/widgets/app_button.dart';
import '../../../../shared/widgets/app_card.dart';
import '../../../../shared/widgets/app_divider.dart';
import '../../../../shared/widgets/app_logo.dart';
import '../../../../shared/widgets/app_password_field.dart';
import '../../../../shared/widgets/app_snackbar.dart';
import '../../../../shared/widgets/app_text_button.dart';
import '../../../../shared/widgets/app_text_field.dart';
import '../../../profile/presentation/controllers/profile_controller.dart';
import '../controllers/auth_controller.dart';

class PatientRegisterScreen extends ConsumerStatefulWidget {
  const PatientRegisterScreen({super.key});

  @override
  ConsumerState<PatientRegisterScreen> createState() => _PatientRegisterScreenState();
}

class _PatientRegisterScreenState extends ConsumerState<PatientRegisterScreen> {
  final _formKey = GlobalKey<FormState>();
  final _nameController = TextEditingController();
  final _emailController = TextEditingController();
  final _phoneController = TextEditingController();
  final _passwordController = TextEditingController();
  final _confirmPasswordController = TextEditingController();
  bool _acceptTerms = false;
  bool _isGoogleSigningIn = false;

  @override
  void dispose() {
    _nameController.dispose();
    _emailController.dispose();
    _phoneController.dispose();
    _passwordController.dispose();
    _confirmPasswordController.dispose();
    super.dispose();
  }

  Future<void> _handleGoogleSignUp() async {
    setState(() => _isGoogleSigningIn = true);

    final authController = ref.read(authControllerProvider.notifier);
    final success = await authController.signInWithGoogle();

    if (!mounted) return;
    setState(() => _isGoogleSigningIn = false);

    if (success) {
      final user = ref.read(authControllerProvider).user;
      if (user != null) {
        ref.read(patientProfileProvider.notifier).syncFromUser(user);
        if (!user.isHealthProfileCompleted) {
          context.go(AppRoutes.createHealthProfile);
        } else {
          context.go(AppRoutes.home);
        }
      } else {
        context.go(AppRoutes.home);
      }
    } else {
      final errorMessage = ref.read(authControllerProvider).errorMessage;
      if (errorMessage != null && errorMessage.isNotEmpty) {
        AppSnackbar.showError(context, errorMessage);
      }
    }
  }

  Future<void> _handleRegister() async {
    if (!_formKey.currentState!.validate()) return;

    if (!_acceptTerms) {
      AppSnackbar.showError(context, 'Please accept the Terms & Privacy Policy to register.');
      return;
    }

    final authController = ref.read(authControllerProvider.notifier);
    final success = await authController.register(
      name: _nameController.text.trim(),
      email: _emailController.text.trim(),
      phoneNumber: _phoneController.text.trim(),
      password: _passwordController.text,
    );

    if (!mounted) return;

    if (success) {
      AppSnackbar.showSuccess(context, 'Account created! Please set up your health profile.');
      context.go(AppRoutes.createHealthProfile);
    } else {
      final errorMessage = ref.read(authControllerProvider).errorMessage ?? 'Registration failed.';
      AppSnackbar.showError(context, errorMessage);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      body: LayoutBuilder(
        builder: (context, constraints) {
          if (constraints.maxWidth >= 960) {
            return _buildDesktopLayout(context);
          }
          return _buildMobileLayout(context);
        },
      ),
    );
  }

  Widget _buildDesktopLayout(BuildContext context) {
    return Row(
      children: [
        // Left Branding & Credibility Hero Panel
        Expanded(
          flex: 6,
          child: Container(
            decoration: const BoxDecoration(
              gradient: LinearGradient(
                colors: [Color(0xFF0F172A), Color(0xFF1E3A8A)],
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
              ),
            ),
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 64, vertical: 48),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Row(
                    children: [
                      Container(
                        padding: const EdgeInsets.all(8),
                        decoration: BoxDecoration(
                          color: Colors.white.withValues(alpha: 0.1),
                          borderRadius: AppRadius.radiusMd,
                          border: Border.all(color: Colors.white.withValues(alpha: 0.2)),
                        ),
                        child: const Icon(Icons.health_and_safety_rounded, color: Colors.white, size: 28),
                      ),
                      AppSpacing.gapHMd,
                      Text(
                        'HealthCare Enterprise',
                        style: AppTypography.titleLarge.copyWith(
                          color: Colors.white,
                          fontWeight: FontWeight.w800,
                          letterSpacing: -0.5,
                        ),
                      ),
                    ],
                  ),

                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const AppBadge(
                        text: 'JOIN OVER 120,000+ PATIENTS',
                        variant: BadgeVariant.secondary,
                        icon: Icons.people_alt_rounded,
                      ),
                      AppSpacing.gapVMd,
                      Text(
                        'Fast, Reliable & Modern Healthcare at Your Fingertips',
                        style: AppTypography.displayLarge.copyWith(
                          color: Colors.white,
                          fontSize: 38,
                          fontWeight: FontWeight.w800,
                          height: 1.15,
                          letterSpacing: -1.0,
                        ),
                      ),
                      AppSpacing.gapVMd,
                      Text(
                        'Create your patient profile to connect instantly with world-class specialists, track vital trends, and manage digital prescriptions.',
                        style: AppTypography.bodyLg.copyWith(
                          color: const Color(0xFF94A3B8),
                          height: 1.5,
                        ),
                      ),
                    ],
                  ),

                  Text(
                    '© 2026 HealthCare Platform Inc. Verified Medical Network.',
                    style: AppTypography.labelSm.copyWith(
                      color: const Color(0xFF64748B),
                    ),
                  ),
                ],
              ),
            ),
          ),
        ),

        // Right Registration Form
        Expanded(
          flex: 5,
          child: Container(
            color: AppColors.surface,
            child: Center(
              child: SingleChildScrollView(
                padding: const EdgeInsets.symmetric(horizontal: 48, vertical: 32),
                child: ConstrainedBox(
                  constraints: const BoxConstraints(maxWidth: 440),
                  child: _buildRegisterForm(context),
                ),
              ),
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildMobileLayout(BuildContext context) {
    return SafeArea(
      child: Center(
        child: SingleChildScrollView(
          padding: AppSpacing.paddingScreenAll,
          child: ConstrainedBox(
            constraints: const BoxConstraints(maxWidth: 440),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const AppLogo(iconSize: 44, fontSize: 24),
                AppSpacing.gapVXl,
                AppCard(
                  padding: const EdgeInsets.all(AppSpacing.xl),
                  child: _buildRegisterForm(context),
                ),
                AppSpacing.gapVLg,
                _buildLoginLink(context),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildRegisterForm(BuildContext context) {
    final authState = ref.watch(authControllerProvider);

    return Form(
      key: _formKey,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        mainAxisSize: MainAxisSize.min,
        children: [
          Text(
            'Create Account',
            style: AppTypography.headlineSm.copyWith(
              color: AppColors.onSurface,
              fontWeight: FontWeight.w800,
            ),
          ),
          AppSpacing.gapVXs,
          Text(
            'Join HealthCare for seamless clinical medical access.',
            style: AppTypography.bodySm.copyWith(
              color: AppColors.onSurfaceVariant,
            ),
          ),
          AppSpacing.gapVLg,

          AppTextField(
            label: 'Full Name',
            hintText: 'Sarah Jenkins',
            controller: _nameController,
            validator: Validators.validateName,
            prefixIcon: const Icon(Icons.person_outline_rounded, size: 20, color: AppColors.outline),
          ),
          AppSpacing.gapVMd,

          AppTextField(
            label: 'Email Address',
            hintText: 'name@example.com',
            controller: _emailController,
            keyboardType: TextInputType.emailAddress,
            validator: Validators.validateEmail,
            prefixIcon: const Icon(Icons.mail_outline_rounded, size: 20, color: AppColors.outline),
          ),
          AppSpacing.gapVMd,

          AppTextField(
            label: 'Phone Number',
            hintText: '+1 555-019-2834',
            controller: _phoneController,
            keyboardType: TextInputType.phone,
            validator: Validators.validatePhone,
            prefixIcon: const Icon(Icons.phone_outlined, size: 20, color: AppColors.outline),
          ),
          AppSpacing.gapVMd,

          AppPasswordField(
            label: 'Password',
            controller: _passwordController,
            validator: Validators.validatePassword,
          ),
          AppSpacing.gapVMd,

          AppPasswordField(
            label: 'Confirm Password',
            controller: _confirmPasswordController,
            validator: (val) => Validators.validateConfirmPassword(_passwordController.text, val),
          ),
          AppSpacing.gapVMd,

          // Terms Checkbox
          Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Checkbox(
                value: _acceptTerms,
                onChanged: (val) {
                  setState(() {
                    _acceptTerms = val ?? false;
                  });
                },
              ),
              Expanded(
                child: Padding(
                  padding: const EdgeInsets.only(top: 12),
                  child: Text(
                    'I agree to the HealthCare Terms of Service and Privacy Policy.',
                    style: AppTypography.bodySm.copyWith(
                      color: AppColors.onSurfaceVariant,
                    ),
                  ),
                ),
              ),
            ],
          ),
          AppSpacing.gapVLg,

          AppButton(
            text: 'Create Account',
            isLoading: authState.isLoading,
            onPressed: _handleRegister,
          ),

          const AppDivider(text: 'OR SIGN UP WITH'),

          Row(
            children: [
              Expanded(
                child: AppButton(
                  text: 'Google',
                  variant: ButtonVariant.outlined,
                  prefixIcon: Icons.g_mobiledata_rounded,
                  isLoading: _isGoogleSigningIn,
                  onPressed: (authState.isLoading || _isGoogleSigningIn)
                      ? null
                      : _handleGoogleSignUp,
                ),
              ),
              AppSpacing.gapHMd,
              Expanded(
                child: AppButton(
                  text: 'Apple',
                  variant: ButtonVariant.outlined,
                  prefixIcon: Icons.apple_rounded,
                  onPressed: (authState.isLoading || _isGoogleSigningIn)
                      ? null
                      : () {
                          AppSnackbar.showInfo(context, 'Apple Sign-In is simulated in mock mode.');
                        },
                ),
              ),
            ],
          ),

          if (MediaQuery.of(context).size.width >= 960) ...[
            AppSpacing.gapVXl,
            _buildLoginLink(context),
          ]
        ],
      ),
    );
  }

  Widget _buildLoginLink(BuildContext context) {
    return Wrap(
      alignment: WrapAlignment.center,
      crossAxisAlignment: WrapCrossAlignment.center,
      children: [
        Text(
          'Already have an account? ',
          style: AppTypography.bodyMd.copyWith(color: AppColors.onSurfaceVariant),
        ),
        AppTextButton(
          text: 'Sign In',
          onPressed: () => context.pop(),
        ),
      ],
    );
  }
}
