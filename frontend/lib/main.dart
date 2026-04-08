import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:url_strategy/url_strategy.dart';
import 'config/theme.dart';
import 'config/routes.dart';
import 'services/theme_service.dart';

void main() {
  setPathUrlStrategy(); // Remove # from URLs
  runApp(const AshickeyApp());
}

class AshickeyApp extends StatelessWidget {
  const AshickeyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return ChangeNotifierProvider(
      create: (_) => ThemeService(),
      child: Consumer<ThemeService>(
        builder: (context, themeService, _) {
          return MaterialApp.router(
            title: 'ashickey{}',
            debugShowCheckedModeBanner: false,
            theme: AppTheme.lightTheme,
            darkTheme: AppTheme.darkTheme,
            themeMode: themeService.themeMode,
            routerConfig: appRouter,
          );
        },
      ),
    );
  }
}
