import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/routing/app_routes.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_typography.dart';
import '../../../../core/utils/validators.dart';
import '../../../../shared/widgets/app_button.dart';
import '../../../../shared/widgets/app_card.dart';
import '../../../../shared/widgets/app_divider.dart';
import '../../../../shared/widgets/app_logo.dart';
import '../../../../shared/widgets/app_password_field.dart';
import '../../../../shared/widgets/app_snackbar.dart';
import '../../../../shared/widgets/app_text_button.dart';
import '../../../../shared/widgets/app_text_field.dart';
import '../controllers/auth_controller.dart';

class PatientLoginScreen extends ConsumerStatefulWidget {
  const PatientLoginScreen({super.key});

  @override
  ConsumerState<PatientLoginScreen> createState() => _PatientLoginScreenState();
}

class _PatientLoginScreenState extends ConsumerState<PatientLoginScreen> {
  final _formKey = GlobalKey<FormState>();
  final _emailController = TextEditingController(text: 'sarah.jenkins@example.com');
  final _passwordController = TextEditingController(text: 'Password123!');

  @override
  void dispose() {
    _emailController.dispose();
    _passwordController.dispose();
    super.dispose();
  }

  Future<void> _handleLogin() async {
    if (!_formKey.currentState!.validate()) return;

    final authController = ref.read(authControllerProvider.notifier);
    final success = await authController.login(
      email: _emailController.text.trim(),
      password: _passwordController.text,
    );

    if (!mounted) return;

    if (success) {
      final user = ref.read(authControllerProvider).user;
      if (user != null && !user.isHealthProfileCompleted) {
        context.go(AppRoutes.createHealthProfile);
      } else {
        context.go(AppRoutes.home);
      }
    } else {
      final errorMessage = ref.read(authControllerProvider).errorMessage ?? 'Login failed.';
      AppSnackbar.showError(context, errorMessage);
    }
  }

  @override
  Widget build(BuildContext context) {
    final authState = ref.watch(authControllerProvider);

    return Scaffold(
      backgroundColor: AppColors.background,
      body: SafeArea(
        child: Center(
          child: SingleChildScrollView(
            padding: AppSpacing.paddingScreenAll,
            child: ConstrainedBox(
              constraints: const BoxConstraints(maxWidth: 440),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const AppLogo(iconSize: 48, fontSize: 26),
                  AppSpacing.gapVXl,
                  AppCard(
                    child: Form(
                      key: _formKey,
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.stretch,
                        children: [
                          Text(
                            'Welcome Back',
                            style: AppTypography.headlineSm.copyWith(
                              color: AppColors.onSurface,
                              fontWeight: FontWeight.w700,
                            ),
                          ),
                          AppSpacing.gapVXs,
                          Text(
                            'Please sign in to access your healthcare portal.',
                            style: AppTypography.bodySm.copyWith(
                              color: AppColors.onSurfaceVariant,
                            ),
                          ),
                          AppSpacing.gapVLg,

                          // Email field
                          AppTextField(
                            label: 'Email Address',
                            hintText: 'name@example.com',
                            controller: _emailController,
                            keyboardType: TextInputType.emailAddress,
                            validator: Validators.validateEmail,
                            prefixIcon: const Icon(Icons.mail_outline_rounded, size: 20, color: AppColors.outline),
                          ),
                          AppSpacing.gapVMd,

                          // Password field
                          AppPasswordField(
                            label: 'Password',
                            controller: _passwordController,
                            validator: Validators.validatePassword,
                          ),
                          AppSpacing.gapVSm,

                          // Forgot Password link
                          Align(
                            alignment: Alignment.centerRight,
                            child: AppTextButton(
                              text: 'Forgot Password?',
                              onPressed: () => context.push(AppRoutes.forgotPassword),
                            ),
                          ),
                          AppSpacing.gapVLg,

                          // Sign In CTA
                          AppButton(
                            text: 'Sign In',
                            isLoading: authState.isLoading,
                            onPressed: _handleLogin,
                          ),

                          // Divider
                          const AppDivider(text: 'OR CONTINUE WITH'),

                          // Social Login Grid
                          Row(
                            children: [
                              Expanded(
                                child: AppButton(
                                  text: 'Google',
                                  variant: ButtonVariant.outlined,
                                  prefixIcon: Icons.g_mobiledata_rounded,
                                  onPressed: () {
                                    AppSnackbar.showInfo(context, 'Google Sign-In is simulated in mock mode.');
                                    _emailController.text = 'sarah.google@example.com';
                                    _passwordController.text = 'GoogleAuth123!';
                                  },
                                ),
                              ),
                              AppSpacing.gapHMd,
                              Expanded(
                                child: AppButton(
                                  text: 'Apple',
                                  variant: ButtonVariant.outlined,
                                  prefixIcon: Icons.apple_rounded,
                                  onPressed: () {
                                    AppSnackbar.showInfo(context, 'Apple Sign-In is simulated in mock mode.');
                                    _emailController.text = 'sarah.apple@example.com';
                                    _passwordController.text = 'AppleAuth123!';
                                  },
                                ),
                              ),
                            ],
                          ),
                        ],
                      ),
                    ),
                  ),
                  AppSpacing.gapVLg,

                  // Register link
                  Wrap(
                    alignment: WrapAlignment.center,
                    crossAxisAlignment: WrapCrossAlignment.center,
                    children: [
                      Text(
                        "Don't have an account? ",
                        style: AppTypography.bodyMd.copyWith(color: AppColors.onSurfaceVariant),
                      ),
                      AppTextButton(
                        text: 'Create an account',
                        onPressed: () => context.push(AppRoutes.register),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}
