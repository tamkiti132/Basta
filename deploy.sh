# エラーが発生した場合はスクリプトを停止
set -e

echo "🚀 デプロイを開始します..."

# メンテナンスモードを有効にする
echo "📝 メンテナンスモードを有効にします..."
php artisan down

# 最新のコードを取得
echo "📥 最新のコードを取得します..."
git pull origin main

# Composerの依存関係をインストール（本番用設定）
echo "📦 Composerの依存関係をインストールします..."
composer install --no-dev --optimize-autoloader --prefer-dist --no-interaction

# フロントエンドの依存関係をインストール
echo "🎨 フロントエンドの依存関係をインストールします..."
npm ci

# フロントエンドをビルド
echo "🔨 フロントエンドをビルドします..."
npm run build

# キャッシュをクリア
echo "🧹 キャッシュをクリアします..."
php artisan optimize:clear

# データベースマイグレーション実行
# 実運用開始後は以下に変更
# php artisan migrate --force
echo "🗄️ データベースマイグレーションを実行します..."
php artisan migrate:fresh --seed --force

# キャッシュを最適化
echo "⚡ キャッシュを最適化します..."
php artisan optimize

# メンテナンスモードを解除
echo "✅ メンテナンスモードを解除します..."
php artisan up

echo "🎉 デプロイが完了しました！"