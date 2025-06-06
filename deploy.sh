# エラーが発生した場合はスクリプトを停止
set -e

echo "🚀 デプロイを開始します..."

# メンテナンスモード開始
echo "🔧 メンテナンスモードを開始..."
php artisan down

# 1. 最新コードを取得
echo "📥 最新コードを取得中..."
git pull origin main

# 2. 依存関係更新
echo "📦 依存関係を更新中..."
composer install --no-dev --optimize-autoloader --prefer-dist --no-interaction

# 3. フロントエンド準備
echo "📦 フロントエンド依存関係を更新中..."
npm ci
echo "🏗️ フロントエンドアセットをビルド中..."
nice -n 20 npm run build

# 4. キャッシュクリア
echo "🧹 キャッシュをクリア中..."
php artisan optimize:clear

# 5. マイグレーション
# 実運用開始後は以下に変更
# php artisan migrate --force
echo "🗄️ データベース更新中..."
php artisan migrate:fresh --seed --force

# 6. キャッシュ作成（本番環境最適化）
echo "⚡ アプリケーションを最適化中..."
php artisan optimize

# メンテナンスモード終了
echo "🔧 メンテナンスモードを終了..."
php artisan up

echo "✅ デプロイが完了しました！"