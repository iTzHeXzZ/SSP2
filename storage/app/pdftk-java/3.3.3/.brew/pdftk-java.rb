class PdftkJava < Formula
  desc "Port of pdftk in java"
  homepage "https://gitlab.com/pdftk-java/pdftk"
  url "https://gitlab.com/pdftk-java/pdftk/-/archive/v3.3.3/pdftk-v3.3.3.tar.gz"
  sha256 "9c947de54658539e3a136e39f9c38ece1cf2893d143abb7f5bf3a2e3e005b286"
  license "GPL-2.0-or-later"
  head "https://gitlab.com/pdftk-java/pdftk.git", branch: "master"

  livecheck do
    url :stable
    regex(/^v?(\d+(?:\.\d+)+)$/i)
  end

  depends_on "gradle" => :build
  depends_on "openjdk@11"

  def install
    system "gradle", "shadowJar", "--no-daemon"
    libexec.install "build/libs/pdftk-all.jar"
    bin.write_jar_script libexec/"pdftk-all.jar", "pdftk", java_version: "11"
    man1.install "pdftk.1"
  end

  test do
    pdf = test_fixtures("test.pdf")
    output_path = testpath/"output.pdf"
    system bin/"pdftk", pdf, pdf, "cat", "output", output_path
    assert output_path.read.start_with?("%PDF")
  end
end
